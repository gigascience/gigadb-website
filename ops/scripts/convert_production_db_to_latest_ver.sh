#!/usr/bin/env bash

# Load environment variables
source ./.env
source ./.secrets

latest=$(date -v-1d +"%Y%m%d")

thedate=${1:-$latest}

# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.production-envs.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

echo "Download and load the production database in to postgreSQL server using file-url-updater"
cd gigadb/app/tools/files-url-updater/
version=$($DOCKER_COMPOSE run --rm pg9_3 bash -c "psql --version | cut -d' ' -f 3 | tr -d '\n'")
$DOCKER_COMPOSE run --rm updater ./yii dataset-files/download-restore-backup --date $thedate

echo "Export production data as text (only the strictly necessary data is exported)"
$DOCKER_COMPOSE run --rm updater pg_dump -h pg9_3 -U gigadb  --clean --create --schema=public --no-privileges --no-tablespaces --dbname gigadb -f sql/gigadbv3_"$thedate"_v"$version".backup

if [[ $? -eq 0  && -f sql/gigadbv3_"$thedate"_v"$version".backup ]];then
  echo "Finished convert production database to postgreSQL $version!"
else
  echo "No upgraded database found, conversion fail!"
fi