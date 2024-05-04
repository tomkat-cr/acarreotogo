SHELL := /bin/bash

# General Commands
help:
	cat Makefile

run:
	sh scripts/start_lnmp.sh run

down:
	sh scripts/start_lnmp.sh down
