#!/bin/sh

REMOTE_DOCKER_HOST=13.37.111.73

# instantiate a container for a PostgreSQL 9.3 instance

docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f docker-compose.yml up -d pg9_3

# Run files-url-updater

./yii dataset-files/download-restore-backup --latest

# Convert backup

latest=$(date -v-1d +"%Y%m%d")
thedate=${1:-$latest}
pg_dump --host=pg9_3 --username=gigadb  --clean --create --schema=public --no-privileges --no-tablespaces --dbname=gigadb --file=sql/gigadbv3_"$thedate"_v"$version".backup

# shut down the PostgreSQL 9.3 instance

docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f docker-compose.yml down


#TODO:
# * [ ] Ensure the files-url-updater config use correct DB credentials
# * [ ] Ensure the DB instance running on dockerhost is reachable from bastion (open a port)
# * [x] Install docker-compose using pip in Ansible playbook
# * [x] Ensure the Dockerhost certificates are downloaded in the bastion server
# * [ ] Get a bash script (this file) to:
#     * [x] download latest DB backup,
#     * [ ] convert it to new format using PosgreSQL 9.3 instance running on dockerhost
#     * [ ] load it in AWS RDS