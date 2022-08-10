#!/usr/bin/env bash

set -exu

docker run --rm --detach --name pg9_3 -p 5432:5432 registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_pg9_3:staging

docker run --rm --add-host=host.docker.internal:host-gateway -v /home/centos:/logs -v /home/centos/downloads:/downloads registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_updater:staging ./yii dataset-files/download-restore-backup --latest --force

docker run --rm --user=1000 --add-host=host.docker.internal:host-gateway -v /home/centos/converted:/converted registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_pg9_3:staging /exportLegacyToTextBackup.sh

docker run --rm --env-file .env -v /home/centos/converted:/converted --entrypoint /restore_database_from_converted_backup.sh registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_s3backup:staging

docker run --rm -e YII_PATH=/var/www/vendor/yiisoft/yii -v /home/centos:/var/www/protected/runtime registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_app:staging /var/www/protected/scripts/prepareConstraints.sh

docker run --rm --env-file .env -v /home/centos:/sql --entrypoint /dropConstraints.sh registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_s3backup:staging

docker run --rm -e YII_PATH=/var/www/vendor/yiisoft/yii -v /home/centos:/var/www/protected/runtime registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_app:staging /var/www/protected/scripts/updateDBSchema.sh

docker run --rm --env-file .env -v /home/centos:/sql --entrypoint /addConstraints.sh registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_s3backup:staging

docker stop pg9_3

rm -f /home/centos/converted/gigadbv3.backup

rm -f /home/centos/downloads/gigadbv3_*.backup

docker system prune --force --all --volumes