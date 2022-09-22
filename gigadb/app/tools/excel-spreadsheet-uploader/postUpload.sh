#!/usr/bin/env bash

PATH=/usr/local/bin:$PATH
export PATH

DOI=$1

if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile
  docker run -e YII_PATH=/var/www/vendor/yiisoft/yii -it registry.gitlab.com/$GITLAB_PROJECT/production_app:$GIGADB_ENV ./protected/yiic files checkUrls --doi=$DOI
  docker run -e YII_PATH=/var/www/vendor/yiisoft/yii -it registry.gitlab.com/$GITLAB_PROJECT/production_app:$GIGADB_ENV ./protected/yiic files updateMD5FileAttributes --doi=$DOI
else
  mkdir -p logs
  docker-compose run --rm  test ./protected/yiic files checkUrls --doi=$DOI
  docker-compose run --rm  test ./protected/yiic files updateMD5FileAttributes --doi=$DOI
fi

