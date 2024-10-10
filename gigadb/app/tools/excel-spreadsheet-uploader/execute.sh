#!/usr/bin/env bash

PATH=/usr/local/bin:$PATH
export PATH

# Colours for text output
RED='\033[0;31m'
NO_COLOR='\033[0m'

currentPath=$(pwd)
if [[ $(uname -n) =~ compute ]];then
  uploadDir=${1:-"$currentPath/uploadDir"}
  cd /home/centos
  . .bash_profile

  if [[ $uploadDir != "/home/centos/uploadDir" ]];then
    mv "$uploadDir"/* /home/centos/uploadDir/
    chown centos:centos /home/centos/uploadDir/*
  fi

  docker run --rm -v /home/centos/uploadDir:/tool/uploadDir -v /home/centos/uploadLogs:/tool/logs registry.gitlab.com/$GITLAB_PROJECT/production_xls_uploader:$GIGADB_ENV ./run.sh

  if [[ -n "$(ls -A /home/centos/uploadDir)" ]];then
    echo -e "${RED}Spreadsheet cannot be uploaded, please check logs!${NO_COLOR}"
    mv /home/centos/uploadDir/* "$uploadDir/"
  fi

  mv /home/centos/uploadLogs/* "$uploadDir/" || true
  chown "$SUDO_USER":"$SUDO_USER" "$uploadDir"/*
  echo "Done."
else
  mkdir -p logs

  echo -e 'RUN EXCEL SPREADSHEET TOOL'
  docker-compose run --rm uploader ./run.sh

  # Check uploadDir is empty in dev environment
  if [ -n "$(ls -A "${currentPath}"/uploadDir)" ];then
    echo -e "${RED}Failed to upload spreadsheet, please check logs!${NO_COLOR}"
  fi

fi


