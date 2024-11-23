#!/usr/bin/env bash

MAKEFILE="Makefile"
FILES_TO_CHANGE=("./deployment/kubernetes-laravel.yml" "./deployment/kubernetes-vue.yml" "./deployment/kubernetes-ws.yml")

VERSION=$(cat $MAKEFILE | grep "VERSION :=" | tr -s " " | tr -d '"' | cut -d " " -f 3)

INC_TYPE="PATCH"

if [ $# -gt 0 ];
then
    case "$1" in
        "++")
            INC_TYPE="MINOR"
            ;;
        "+++")
            INC_TYPE="MAJOR"
            ;;
        *)
            ;;
    esac
fi

# Split version into an array
IFS='.' read -r -a SUB_VERSIONS <<< "$VERSION"

# Extract MAJOR, MINOR, and PATCH
MAJOR=${SUB_VERSIONS[0]}
MINOR=${SUB_VERSIONS[1]}
PATCH=${SUB_VERSIONS[2]}


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

NEW_VERSION="$MAJOR.$MINOR.$PATCH"

if [[ -f $MAKEFILE ]];
then 
    sed -i "s/${VERSION}/${NEW_VERSION}/g" "$MAKEFILE"
fi


for file in "${FILES_TO_CHANGE[@]}";
do
    sed -i "s/v${VERSION}/v${NEW_VERSION}/g" "$file"
done

echo "Version changed to ${NEW_VERSION}"

