#!/usr/bin/env bash

# bail out upon error
set -e

# bail out if an unset variable is used
set -u


# Load .env se we can access CSV_DIR
source "./.env"


# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.staging.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

# create database if not existing
$DOCKER_COMPOSE run --rm test bash -c "psql -h database -U gigadb -c 'create database gigadb_test'" || true

# generate migrations 
$DOCKER_COMPOSE run --rm js bash -c "node /var/www/ops/scripts/csv_yii_migration.js test"

# and run them
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate to 300000_000000 --connectionID=testdb --migrationPath=application.migrations.admin --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate mark 000000_000000 --connectionID=testdb --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate --connectionID=testdb --migrationPath=application.migrations.schema --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate --connectionID=testdb --migrationPath=application.migrations.data.test --interactive=0
# export a binary dump
$DOCKER_COMPOSE run --rm test bash -c "pg_dump --no-owner -U gigadb -h database -p 5432 -F custom -d gigadb_test -f /var/www/sql/gigadb_testdata.pgdmp"