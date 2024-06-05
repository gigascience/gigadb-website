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

# load the upstream backup into RDS
docker run --rm --env-file .env -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf --entrypoint /restore_database_from_s3_backup.sh registry.gitlab.com/$GITLAB_PROJECT_NAME/production_s3backup:$GIGADB_ENVIRONMENT

# Create the drop and add constraints queries that need to be run before and after schema migration respectively
docker run --rm -e YII_PATH=/var/www/vendor/yiisoft/yii -v /home/centos:/var/www/protected/runtime registry.gitlab.com/$GITLAB_PROJECT_NAME/production_app:$GIGADB_ENVIRONMENT /var/www/protected/scripts/prepareConstraints.sh

# drop constraints/indexes/triggers before running migrations
docker run --rm --env-file .env -v /home/centos:/sql --entrypoint /dropConstraints.sh registry.gitlab.com/$GITLAB_PROJECT_NAME/production_s3backup:$GIGADB_ENVIRONMENT

# Run migrations
docker run --rm -e YII_PATH=/var/www/vendor/yiisoft/yii -v /home/centos:/var/www/protected/runtime registry.gitlab.com/$GITLAB_PROJECT_NAME/production_app:$GIGADB_ENVIRONMENT /var/www/protected/scripts/updateDBSchema.sh

#  Restore constraints/indexes/triggers
docker run --rm --env-file .env -v /home/centos:/sql --entrypoint /addConstraints.sh registry.gitlab.com/$GITLAB_PROJECT_NAME/production_s3backup:$GIGADB_ENVIRONMENT

# Housekeeping

#docker stop pg9_3

rm -f /home/centos/converted/gigadbv3*.backup

rm -f /home/centos/downloads/gigadbv3*.backup