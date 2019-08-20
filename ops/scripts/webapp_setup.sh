#!/usr/bin/env bash

set -e -u

source "./.env"

if [ $GIGADB_ENV == "dev" ];then
	composer install -o
elif [ $GIGADB_ENV == "CI" ];then
	composer install -a
elif [ $GIGADB_ENV == "staging" ];then
	composer install -a --no-dev
fi



