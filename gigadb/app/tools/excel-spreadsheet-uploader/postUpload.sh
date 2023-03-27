#!/usr/bin/env bash

set -e

PATH=/usr/local/bin:$PATH
export PATH

DOI=$1

if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile
  docker run --rm registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:latest ./yii update/file-size --doi=$DOI | tee /home/centos/uploadLogs/updating-file-size-$DOI.txt
  docker run --rm registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:latest ./yii check/valid-urls --doi=$DOI | tee /home/centos/uploadLogs/invalid-urls-$DOI.txt
  docker run -e YII_PATH=/var/www/vendor/yiisoft/yii registry.gitlab.com/$GITLAB_PROJECT/production_app:$GIGADB_ENV ./protected/yiic files updateMD5FileAttributes --doi=$DOI
else
  mkdir -p logs
  docker-compose run --rm  test ./protected/yiic files updateMD5FileAttributes --doi=$DOI
  cd gigadb/app/tools/files-metadata-console
  ./yii check/valid-urls --doi=$DOI | tee runtime/logs/invalid-urls-$DOI.txt
  ./yii update/file-size --doi=$DOI | tee runtime/logs/updating-file-size-$DOI.txt
fi

