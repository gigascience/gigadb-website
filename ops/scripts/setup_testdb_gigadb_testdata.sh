#!/usr/bin/env bash

# bail out upon error
set -e
# bail out if an unset variable is used
set -u

# Use database variables in .secrets
set -a
source ./.secrets
set +a

# create test database if not existing
docker-compose run --rm test bash -c "psql -h database -U gigadb -c 'create database gigadb_testdata'" || true

# generate migrations
docker run -v `pwd`:/var/www node:14.9.0-buster bash -c "node /var/www/ops/scripts/csv_yii_migration.js gigadb_testdata"

# and run them
docker-compose run --rm  application ./protected/yiic migrate to 300000_000000 --connectionID=testdb --migrationPath=application.migrations.admin --interactive=0
docker-compose run --rm  application ./protected/yiic migrate mark 000000_000000 --connectionID=testdb --interactive=0
docker-compose run --rm  application ./protected/yiic migrate --connectionID=testdb --migrationPath=application.migrations.schema --interactive=0
docker-compose run --rm  application ./protected/yiic migrate --connectionID=testdb --migrationPath=application.migrations.data.gigadb_testdata --interactive=0

# export a binary dump
docker-compose run --rm test bash -c "PGPASSWORD=$GIGADB_PASSWORD pg_dump --no-owner -U gigadb -h database -p 5432 -F custom -d gigadb_testdata -f /var/www/sql/gigadb_testdata.pgdmp"
