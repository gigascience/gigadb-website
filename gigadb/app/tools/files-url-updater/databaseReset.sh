#!/usr/bin/env bash

set -exu

source .env

# Calculate dates
latest=$(date --date="1 days ago" +"%Y%m%d")
if [ -z ${1+x} ];then
  backupDate=$latest
elif [ $1 == "latest" ];then
  backupDate=$latest
else
  backupDate=$1
fi

# Stop any existing legacy database server if it hasn't done so in previous run (e.g: due to errors)
docker stop pg9_3 || true

# Spin up legacy database (PosgresQL 9.3)
# Make sure the port 5432 is accessible from host
docker run --rm --detach --name pg9_3 -p 5432:5432 registry.gitlab.com/$GITLAB_PROJECT_NAME/production_pg9_3:$GIGADB_ENVIRONMENT

# Download/restore legacy database on the legacy server
# make sure to define --add-host, so the files-url-updater command can connect to the host on port 5432 using ``host.docker.internal``
docker run --rm --add-host=host.docker.internal:host-gateway -v /home/centos:/logs -v /home/centos/downloads:/downloads registry.gitlab.com/$GITLAB_PROJECT_NAME/production_updater:$GIGADB_ENVIRONMENT ./yii dataset-files/download-restore-backup --date $backupDate --force

# Export legacy database as text in new format modern version of Pg can read
docker run --rm --user=1000 --add-host=host.docker.internal:host-gateway -v /home/centos/converted:/converted registry.gitlab.com/$GITLAB_PROJECT_NAME/production_pg9_3:$GIGADB_ENVIRONMENT /exportLegacyToTextBackup.sh /converted/gigadbv3_${backupDate}.backup

# load the converted backup into RDS
docker run --rm --env-file .env -v /home/centos/converted:/converted --entrypoint /restore_database_from_converted_backup.sh registry.gitlab.com/$GITLAB_PROJECT_NAME/production_s3backup:$GIGADB_ENVIRONMENT /converted/gigadbv3_${backupDate}.backup

# Create the drop and add constraints queries that need to be run before and after schema migration respectively
docker run --rm -e YII_PATH=/var/www/vendor/yiisoft/yii -v /home/centos:/var/www/protected/runtime registry.gitlab.com/$GITLAB_PROJECT_NAME/production_app:$GIGADB_ENVIRONMENT /var/www/protected/scripts/prepareConstraints.sh

# drop constraints/indexes/triggers before running migrations
docker run --rm --env-file .env -v /home/centos:/sql --entrypoint /dropConstraints.sh registry.gitlab.com/$GITLAB_PROJECT_NAME/production_s3backup:$GIGADB_ENVIRONMENT

# Run migrations
docker run --rm -e YII_PATH=/var/www/vendor/yiisoft/yii -v /home/centos:/var/www/protected/runtime registry.gitlab.com/$GITLAB_PROJECT_NAME/production_app:$GIGADB_ENVIRONMENT /var/www/protected/scripts/updateDBSchema.sh

#  Restore constraints/indexes/triggers
docker run --rm --env-file .env -v /home/centos:/sql --entrypoint /addConstraints.sh registry.gitlab.com/$GITLAB_PROJECT_NAME/production_s3backup:$GIGADB_ENVIRONMENT

# Housekeeping

docker stop pg9_3

rm -f /home/centos/converted/gigadbv3*.backup

rm -f /home/centos/downloads/gigadbv3*.backup