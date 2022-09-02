#!/usr/bin/env bash

# bail out upon error
set -e
# bail out if an unset variable is used
set -u

# Use database variables in .secrets
set -a
source ./.env
source ./.secrets
set +a

# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_WEBAPP_DOCKER -f ops/deployment/docker-compose.production-envs.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

# export a binary dump
$DOCKER_COMPOSE run --rm test bash -c "PGPASSWORD=$GIGADB_PASSWORD pg_dump --no-owner -U gigadb -h database -p 5432 -F custom -d gigadb -f /var/www/sql/gigadb.pgdmp"
