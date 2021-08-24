#!/usr/bin/env bash

source ./.env
source ./.secrets

latest=$(date -v-1d +"%Y%m%d")

# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.production-envs.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

echo "Go into docker container and store the postgreSQL version"
version=$($DOCKER_COMPOSE run --rm test bash -c "psql --version | cut -d' ' -f 3")

echo "Download the production database using file-url-updater"
cd gigadb/app/tools/files-url-updater/
docker-compose run --rm updater ./yii dataset-files/download-restore-backup --latest --norestore
cd ../../../../

echo "Create production database for the version upgrade"
$DOCKER_COMPOSE run --rm test bash -c "psql -h database -U gigadb -c 'create database gigadbv3_production'"

echo "Load the production database in PostgreSQL"
$DOCKER_COMPOSE run --rm test bash -c "PGPASSWORD=$GIGADB_PASSWORD pg_restore -v -U gigadb -h database -p 5432 -d gigadbv3_production /var/www/gigadb/app/tools/files-url-updater/sql/gigadbv3_${latest}.backup"

echo "Dump production database"
$DOCKER_COMPOSE run --rm test bash -c "PGPASSWORD=$GIGADB_PASSWORD pg_dump -v -U gigadb -h database -p 5432 -Fc -d gigadbv3_production -f /var/www/gigadb/app/tools/files-url-updater/sql/gigadbv3_${latest}_${version}.backup"
$DOCKER_COMPOSE run --rm test bash -c "PGPASSWORD=$GIGADB_PASSWORD pg_dump -v -U gigadb -h database -p 5432 -d gigadbv3_production -f /var/www/gigadb/app/tools/files-url-updater/sql/gigadbv3_${latest}_${version}.sql"

echo "Drop production database after conversion"
$DOCKER_COMPOSE run --rm test bash -c "psql -h database -U gigadb -c 'drop database gigadbv3_production'"
#
if [[ -f gigadb/app/tools/files-url-updater/sql/gigadbv3_"$latest"_"$version".backup ]];then
  echo "Finished convert production database to postgreSQL version" "$version"
else
  echo "No upgraded database dump found, conversion fail!"
fi
