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
$DOCKER_COMPOSE run --rm test bash -c "psql -h database -U gigadb -c 'create database gigadb'" || true

# generate migrations (by default "test" data is loaded in the dev db)
# docker run -v `pwd`:/var/www node:14.9.0-buster bash -c "node /var/www/ops/scripts/csv_yii_migration.js test"
$DOCKER_COMPOSE run --rm js bash -c "node /var/www/ops/scripts/csv_yii_migration.js prod_like"

# and run them
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate to 300000_000000 --connectionID=db --migrationPath=application.migrations.admin --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate mark 000000_000000 --connectionID=db --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.schema --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.data.prod_like --interactive=0

# run migration for FUW database
$DOCKER_COMPOSE exec -T console /app/yii migrate/fresh --interactive=0
