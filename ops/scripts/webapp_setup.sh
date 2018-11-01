#!/usr/bin/env bash

set -e -u

source "./.env"

if [ $GIGADB_ENV == "dev" ];then
	composer install -o
	cp ops/configuration/nginx-conf/le.dev.ini /etc/letsencrypt/cli.ini
elif [ $GIGADB_ENV == "CI" ];then
	composer install -a
elif [ $GIGADB_ENV == "staging" ];then
	cp ops/configuration/nginx-conf/le.staging.ini /etc/letsencrypt/cli.ini
fi

./protected/yiic lesscompiler

