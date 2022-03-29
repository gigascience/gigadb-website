#!/usr/bin/env bash

set +e
set +x

source ./.env

export PATH=/usr/local/bin/:$PATH
# preflight checks
which docker-compose

# instantiate a container for a PostgreSQL 9.3 instance

docker-compose up -d pg9_3
sleep 5
docker-compose ps
docker-compose logs pg9_3

# Calculate dates
latest=$(date --date="1 days ago" +"%Y%m%d")
twoDaysAgo=$(date --date="2 days ago" +"%Y%m%d")
threeDaysAgo=$(date --date="3 days ago" +"%Y%m%d")
thedate=${1:-$latest}

# Default converted backup and legacy postgres version
default_db = "/home/centos/database_bootstrap.backup"
version=$(docker-compose run --rm updater psql --version | cut -d' ' -f 3 | tr -d '\n\r' )

# Run files-url-updater
echo yes | docker-compose run --rm updater ./yii dataset-files/download-restore-backup --latest
downloadRestoreStatus=$?

# Convert backup using legacy postgresql client
if [[ $downloadRestoreStatus -eq 0 ]];then
  docker-compose run --rm updater pg_dump --host=pg9_3 -p 5432  --username=gigadb  --clean --create --schema=public --no-privileges --no-tablespaces --dbname=gigadb --file=converted/gigadbv3_${thedate}_v${version}.backup
  convertStatus=$?
fi

# shut down the PostgreSQL 9.3 instance
docker-compose down -v

# Terminate other connections to RDS instance
export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c  "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname='gigadb';"

# Load the dump in RDS using native postgresql client
if [[ $downloadRestoreStatus -eq 0 && $convertStatus -eq 0 ]];then
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "drop database if exists $DB_PG_DATABASE"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "create database $DB_PG_DATABASE owner $DB_PG_USER"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -h $DB_PG_HOST -p 5432 < converted/gigadbv3_${thedate}_v${version}.backup
  echo "Loaded gigadbv3_${thedate}_v${version}.backup"
elif [[ -f converted/gigadbv3_${twoDaysAgo}_v${version}.backup ]];then
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "drop database if exists $DB_PG_DATABASE"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "create database $DB_PG_DATABASE owner $DB_PG_USER"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -h $DB_PG_HOST -p 5432 < converted/gigadbv3_${twoDaysAgo}_v${version}.backup
  echo "Loaded gigadbv3_${twoDaysAgo}_v${version}.backup from two days ago"
elif [[ -f converted/gigadbv3_${threeDaysAgo}_v${version}.backup ]];then
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "drop database if exists $DB_PG_DATABASE"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "create database $DB_PG_DATABASE owner $DB_PG_USER"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -h $DB_PG_HOST -p 5432 < converted/gigadbv3_${threeDaysAgo}_v${version}.backup
  echo "Loaded gigadbv3_${threeDaysAgo}_v${version}.backup from three days ago"
else
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "drop database if exists $DB_PG_DATABASE"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "create database $DB_PG_DATABASE owner $DB_PG_USER"
  export PGPASSWORD=$DB_PG_PASSWORD; psql -U $DB_PG_USER -h $DB_PG_HOST -p 5432 < /home/centos/database_bootstrap.backup
  echo "Loaded default backup /home/centos/database_bootstrap.backup"
fi

#TODO:
# * [ ] Ensure the files-url-updater config use correct DB credentials
# * [ ] Ensure the DB instance running on dockerhost is reachable from bastion (open a port)
# * [x] Install docker-compose using pip in Ansible playbook
# * [x] Ensure the Dockerhost certificates are downloaded in the bastion server
# * [ ] Get a bash script (this file) to:
#     * [x] download latest DB backup,
#     * [ ] convert it to new format using PosgreSQL 9.3 instance running on dockerhost
#     * [ ] load it in AWS RDS