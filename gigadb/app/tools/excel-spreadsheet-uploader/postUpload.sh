#!/usr/bin/env bash

set -e

PATH=/usr/local/bin:$PATH
export PATH

DOI=$1

if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile
  docker run -e YII_PATH=/var/www/vendor/yiisoft/yii registry.gitlab.com/$GITLAB_PROJECT/production_app:$GIGADB_ENV ./protected/yiic files checkUrls --doi=$DOI
  docker run -e YII_PATH=/var/www/vendor/yiisoft/yii registry.gitlab.com/$GITLAB_PROJECT/production_app:$GIGADB_ENV ./protected/yiic files updateMD5FileAttributes --doi=$DOI
  docker run -it registry.gitlab.com/$GITLAB_PROJECT/production_tool:$GIGADB_ENV /app/yii readme/create --doi "$DOI" --outdir /home/curators
else
  mkdir -p logs
  docker-compose run --rm test ./protected/yiic files checkUrls --doi=$DOI
  docker-compose run --rm test ./protected/yiic files updateMD5FileAttributes --doi=$DOI
  docker-compose run --rm tool /app/yii_test readme/create --doi "$DOI" --outdir /home/curators
fi

