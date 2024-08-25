#!/usr/bin/env bash

set -e

PATH=/usr/local/bin:$PATH
export PATH

DOI=$1

if [ -z "$DOI" ];then
  if [[ $(uname -n) =~ compute ]]; then
    echo -e "Usage: /usr/local/bin/filesMetaToDb <DOI>\n"
  else
    echo -e "Usage: ./filesMetaToDb.sh <DOI>\n "
  fi
  exit 1;
fi

if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile

  echo -e "Updating md5 checksum values as file attributes for $DOI"
  docker run --rm -v /var/share/gigadb/metadata:/var/share/gigadb/metadata "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/md5-values --doi="$DOI"
  echo -e "Updating file sizes for $DOI"
  docker run --rm -v /var/share/gigadb/metadata:/var/share/gigadb/metadata "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/file-sizes --doi="$DOI"

else
  echo -e "Updating md5 checksum values as file attributes for $DOI"
  docker-compose run --rm files-metadata-console ./yii update/md5-values --doi="$DOI"
  echo -e "Updating file sizes for $DOI"
  docker-compose run --rm files-metadata-console ./yii update/file-sizes --doi="$DOI"
fi

echo -e "Updated file metadata for $DOI in database"
