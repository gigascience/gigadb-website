#!/usr/bin/env bash

set -e -u -x

source "./.env"

if [ $COMPOSE_FILE == "ops/deployment/docker-compose.yml:ops/deployment/docker-compose.production.yml" ];then
	echo "* Production mode *"
	composer install -a
elif [ $COMPOSE_FILE == "ops/deployment/docker-compose.yml:ops/deployment/docker-compose.ci.yml" ];then
	echo "* CI mode *"
	composer install -a
elif [ $COMPOSE_FILE == "ops/deployment/docker-compose.yml:ops/deployment/docker-compose.prof.yml" ];then
	echo "* Profiling mode *"
	composer install -a
else
	echo "* Development mode *"
	composer install -o
fi

./protected/yiic lesscompiler

