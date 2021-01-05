#!/usr/bin/env bash

# create test database if not existing
docker-compose run --rm test bash -c "psql -h database -U gigadb -c 'create database production_like'" || true

# generate migrations
docker run -v `pwd`:/var/www node:14.9.0-buster bash -c "node /var/www/ops/scripts/csv_yii_migration.js production_like"

# and run them
docker-compose run --rm  application ./protected/yiic migrate to 300000_000000 --connectionID=testdb_production_like --migrationPath=application.migrations.admin --interactive=0
docker-compose run --rm  application ./protected/yiic migrate mark 000000_000000 --connectionID=testdb_production_like --interactive=0
docker-compose run --rm  application ./protected/yiic migrate --connectionID=testdb_production_like --migrationPath=application.migrations.schema --interactive=0
docker-compose run --rm  application ./protected/yiic migrate --connectionID=testdb_production_like --migrationPath=application.migrations.data.production_like --interactive=0

# export a binary dump
docker-compose run --rm test bash -c "pg_dump --no-owner -U gigadb -h database -p 5432 -F custom -d production_like -f /var/www/sql/production_like.pgdmp"
