#!/usr/bin/env bash

set -e -u

source "./.env"

if [ $GIGADB_ENV == "dev" ];then
	echo "* Development mode *"
	composer install -o
else
	echo "* Production mode *"
	composer install -a
fi

./protected/yiic lesscompiler

