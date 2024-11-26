#!/usr/bin/env bash

MAKEFILE="Makefile"
LARAVEL="./deployment/kubernetes-laravel.yml"
VUE="./deployment/kubernetes-vue.yml"
WS="./deployment/kubernetes-ws.yml"

# Extract current versions from the Makefile
VERSION_LARAVEL=$(grep "VERSION_LARAVEL :=" "$MAKEFILE" | tr -s " " | cut -d " " -f 3 | tr -d '"')
VERSION_VUE=$(grep "VERSION_VUE :=" "$MAKEFILE" | tr -s " " | cut -d " " -f 3 | tr -d '"')
VERSION_WS=$(grep "VERSION_WS :=" "$MAKEFILE" | tr -s " " | cut -d " " -f 3 | tr -d '"')

# Default increment type and component to change
INC_TYPE="PATCH"
TO_CHANGE="A" # A = All (default), L = Laravel, V = Vue, W = WebSocket
VERSION=""

show_help() {
    echo "Usage: $0 [COMPONENT] [INCREMENT_TYPE]"
    echo ""
    echo "Options:"
    echo "  COMPONENT:"
    echo "    laravel | l | api       Update Laravel version"
    echo "    vue     | v | web       Update Vue version"
    echo "    websocket | w | ws      Update WebSocket version"
    echo "    (default: all components)"
    echo ""
    echo "  INCREMENT_TYPE:"
    echo "    +       Increment PATCH version (default)"
    echo "    ++      Increment MINOR version"
    echo "    +++     Increment MAJOR version"
    echo ""
    echo "  General:"
    echo "    -h      Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0                  Increment PATCH version for all components"
    echo "  $0 laravel ++       Increment MINOR version for Laravel"
    echo "  $0 vue +++          Increment MAJOR version for Vue"
    echo "  $0 ws               Increment PATCH version for WebSocket"
    exit 0
}

# Parse arguments
if [ $# -gt 0 ]; then
    case "$1" in
        "-h") show_help ;;
        "++") INC_TYPE="MINOR" ;;
        "+++") INC_TYPE="MAJOR" ;;
        "laravel"|"l"|"api") TO_CHANGE="L"; VERSION=$VERSION_LARAVEL ;;
        "vue"|"v"|"web") TO_CHANGE="V"; VERSION=$VERSION_VUE ;;
        "websocket"|"w"|"ws") TO_CHANGE="W"; VERSION=$VERSION_WS ;;
        *) ;;
    esac
    if [ $# -gt 1 ]; then
        case "$2" in
            "++") INC_TYPE="MINOR" ;;
            "+++") INC_TYPE="MAJOR" ;;
            "laravel"|"l"|"api") TO_CHANGE="L"; VERSION=$VERSION_LARAVEL ;;
            "vue"|"v"|"web") TO_CHANGE="V"; VERSION=$VERSION_VUE ;;
            "websocket"|"w"|"ws") TO_CHANGE="W"; VERSION=$VERSION_WS ;;
            *) ;;
        esac
    fi
fi

# Default version to all components if not specified
if [ -z "$VERSION" ]; then
    VERSION=$VERSION_LARAVEL # Start with Laravel as the default
fi

# Split version into an array
IFS='.' read -r -a SUB_VERSIONS <<< "$VERSION"

# Extract MAJOR, MINOR, and PATCH components
MAJOR=${SUB_VERSIONS[0]}
MINOR=${SUB_VERSIONS[1]}
PATCH=${SUB_VERSIONS[2]}

# Increment the version based on the specified increment type
case $INC_TYPE in
    MAJOR)
        ((MAJOR+=1))
        MINOR=0
        PATCH=0
        ;;
    MINOR)
        ((MINOR+=1))
        PATCH=0
        ;;
    PATCH)
        ((PATCH+=1))
        ;;
    *)
        echo "Unknown increment type: $INC_TYPE"
        exit 1
        ;;
esac

# Form the new version string
NEW_VERSION="$MAJOR.$MINOR.$PATCH"

# Update the Makefile
if [[ -f $MAKEFILE ]]; then
    case "$TO_CHANGE" in
        "L")
            sed -i "s/VERSION_LARAVEL := .*/VERSION_LARAVEL := \"$NEW_VERSION\"/" "$MAKEFILE"
            ;;
        "V")
            sed -i "s/VERSION_VUE := .*/VERSION_VUE := \"$NEW_VERSION\"/" "$MAKEFILE"
            ;;
        "W")
            sed -i "s/VERSION_WS := .*/VERSION_WS := \"$NEW_VERSION\"/" "$MAKEFILE"
            ;;
        "A")
            sed -i "s/VERSION_LARAVEL := .*/VERSION_LARAVEL := \"$NEW_VERSION\"/" "$MAKEFILE"
            sed -i "s/VERSION_VUE := .*/VERSION_VUE := \"$NEW_VERSION\"/" "$MAKEFILE"
            sed -i "s/VERSION_WS := .*/VERSION_WS := \"$NEW_VERSION\"/" "$MAKEFILE"
            ;;
    esac
fi

# Update Kubernetes files
FILES_TO_CHANGE=()
case "$TO_CHANGE" in
    "L") FILES_TO_CHANGE=("$LARAVEL") ;;
    "V") FILES_TO_CHANGE=("$VUE") ;;
    "W") FILES_TO_CHANGE=("$WS") ;;
    "A") FILES_TO_CHANGE=("$LARAVEL" "$VUE" "$WS") ;;
esac

for file in "${FILES_TO_CHANGE[@]}"; do
    if [[ -f $file ]]; then
        sed -i "s/v$VERSION/v$NEW_VERSION/g" "$file"
        echo "Updated $file to version v$NEW_VERSION"
    else
        echo "Warning: $file not found. Skipping."
    fi
done

echo "Version updated to $NEW_VERSION in $TO_CHANGE components."

