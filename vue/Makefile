
ifeq ($(OS),Windows_NT)
    DEV_TOOL := $(shell where bun >nul 2>&1 && echo bun || (where npm >nul 2>&1 && echo npm) || echo none)
else
    DEV_TOOL := $(shell command -v bun >/dev/null 2>&1 && echo bun || (command -v npm >/dev/null 2>&1 && echo npm) || echo none)
endif

ifeq ($(DEV_TOOL),none)
	$(error No dev tool found (bun or npm))
endif

dev:
	${DEV_TOOL} run dev

build:
	${DEV_TOOL} run build

install:
	${DEV_TOOL} install
