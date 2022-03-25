#!/bin/sh

set +ex

source ./.env

# instantiate a container for a PostgreSQL 9.3 instance

docker-compose --tlsverify -H=$DOCKERHOST_PUBLIC_IP:2376 -f docker-compose.yml up -d pg9_3
sleep 5
docker-compose --tlsverify -H=$DOCKERHOST_PUBLIC_IP:2376 -f docker-compose.yml ps
docker-compose --tlsverify -H=$DOCKERHOST_PUBLIC_IP:2376 -f docker-compose.yml logs pg9_3

# init the database

psql -h $DOCKERHOST_PRIVATE_IP -p 6543 -U postgres < ./sql/bootstrap_gigadb.sql

# Run files-url-updater

echo yes | ./yii dataset-files/download-restore-backup --latest

# Convert backup

latest=$(date --date="1 days ago" +"%Y%m%d")
thedate=${1:-$latest}
version=$(psql --version | cut -d' ' -f 3 | tr -d '\n' )
pg_dump --host=$DOCKERHOST_PRIVATE_IP -p 6543  --username=gigadb  --clean --create --schema=public --no-privileges --no-tablespaces --dbname=gigadb --file=gigadbv3_"$thedate"_v"$version".backup

# shut down the PostgreSQL 9.3 instance

docker-compose --tlsverify -H=$DOCKERHOST_PUBLIC_IP:2376 -f docker-compose.yml down -v


#TODO:
# * [ ] Ensure the files-url-updater config use correct DB credentials
# * [ ] Ensure the DB instance running on dockerhost is reachable from bastion (open a port)
# * [x] Install docker-compose using pip in Ansible playbook
# * [x] Ensure the Dockerhost certificates are downloaded in the bastion server
# * [ ] Get a bash script (this file) to:
#     * [x] download latest DB backup,
#     * [ ] convert it to new format using PosgreSQL 9.3 instance running on dockerhost
#     * [ ] load it in AWS RDS