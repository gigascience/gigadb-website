#!/usr/bin/env bash

# bail out upon error
set -e

# bail out if an unset variable is used
set -u


# Load .env se we can access CSV_DIR
source "./.env"

# create database if not existing
docker-compose run --rm test bash -c "psql -h database -U gigadb -c 'create database gigadb'" || true

# generate migrations (by default "test" data is loaded in the dev db)
docker run -v `pwd`:/var/www node:14.9.0-buster bash -c "node /var/www/ops/scripts/csv_yii_migration.js test"

# and run them
docker-compose run --rm  application ./protected/yiic migrate to 300000_000000 --connectionID=db --migrationPath=application.migrations.admin --interactive=0
docker-compose run --rm  application ./protected/yiic migrate mark 000000_000000 --connectionID=db --interactive=0
docker-compose run --rm  application ./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.schema --interactive=0
docker-compose run --rm  application ./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.data.test --interactive=0

# run migration for FUW database
docker-compose exec -T console /app/yii migrate/fresh --interactive=0
