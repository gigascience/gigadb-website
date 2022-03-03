#!/usr/bin/env bash

# bail out upon error
set -e

# bail out if an unset variable is used
set -u


# Load .env se we can access CSV_DIR
source "./.env"

# Accepting parameter and set a default value for it for selecting the kind of canned DB data to populate the database
# the value should match directory name under the "data" directory at the root of the project
dbSet=${1:-"gigadb_testdata"}

# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.production-envs.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

# create database if not existing
$DOCKER_COMPOSE run --rm test bash -c "psql -h database -U gigadb -c 'create database gigadb'" || true

# generate migrations (by default "test" data is loaded in the dev db)
# docker run -v `pwd`:/var/www node:14.9.0-buster-slim bash -c "node /var/www/ops/scripts/csv_yii_migration.js test"
$DOCKER_COMPOSE run --rm js bash -c "node /var/www/ops/scripts/csv_yii_migration.js $dbSet"

# create sql script to drop and recreate constraints, indexes and triggers
docker-compose run --rm test ./protected/yiic custommigrations preparedropcreateconstraints

# and run schema migration
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate to 300000_000000 --connectionID=db --migrationPath=application.migrations.admin --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate mark 000000_000000 --connectionID=db --interactive=0
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.schema --interactive=0

# Drop constraints, indexes and triggers to allow data migrations
docker-compose run --rm test psql -h database -U gigadb < protected/runtime/dropConstraintsQuery.sql
docker-compose run --rm test psql -h database -U gigadb < protected/runtime/dropIndexQuery.sql
docker-compose run --rm test psql -h database -U gigadb < protected/runtime/dropTriggerQuery.sql

# Perform data migrations
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.data.$dbSet --interactive=0

# update the url for project images and dataset images
$DOCKER_COMPOSE run --rm  application ./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.fix_import --interactive=0

# Restore constraints, indexes and triggers
docker-compose run --rm test psql -h database -U gigadb < protected/runtime/addConstraintsQuery.sql
docker-compose run --rm test psql -h database -U gigadb < protected/runtime/addIndexQuery.sql
docker-compose run --rm test psql -h database -U gigadb < protected/runtime/addTriggerQuery.sql

# run migration for FUW database
$DOCKER_COMPOSE exec -T console /app/yii migrate/fresh --interactive=0

# Fix sequences
$DOCKER_COMPOSE run --rm application ./protected/yiic sequencefixer fixAll