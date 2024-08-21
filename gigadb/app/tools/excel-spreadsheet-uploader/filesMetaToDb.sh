#!/usr/bin/env bash

set -e

PATH=/usr/local/bin:$PATH
export PATH

DOI=$1

currentPath=$(pwd)
userOutputDir="$currentPath/uploadDir"

if [[ $(uname -n) =~ compute ]];then
  outputDir="/home/centos/uploadLogs"
else
  # For creating readme file on dev environment
  # /home/curators is mapped to gigadb/app/tools/readme-generator/runtime/curators directory
  outputDir="/home/curators"
fi

if [ -z "$DOI" ];then
  if [[ $(uname -n) =~ compute ]]; then
    echo -e "Usage: /usr/local/bin/filesMetaToDb <DOI>\n"
  else
    echo -e "Usage: ./filesMetaToDb.sh <DOI>\n "
  fi
  exit 1;
fi

updateMD5ChecksumStartMessage="\n* About to update files' MD5 Checksum as file attribute for $DOI"
updateMD5ChecksumEndMessage="\nDone with updating files' MD5 Checksum as file attribute for $DOI. Process status is saved in file: $outputDir/updating-md5checksum-$DOI.txt"

updateFileSizeStartMessage="\n* About to update files' size for $DOI"
updateFileSizeEndMessage="\nDone with updating files' size for $DOI. Nb of successful changes saved in file: $outputDir/updating-file-size-$DOI.txt"

if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile

  echo -e "$updateMD5ChecksumStartMessage"
  docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/md5-values --doi="$DOI" | tee "$outputDir/updating-md5checksum-$DOI.txt"
  echo -e "$updateMD5ChecksumEndMessage"

  echo -e "$updateFileSizeStartMessage"
  docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/file-sizes --doi="$DOI" | tee "$outputDir/updating-file-size-$DOI.txt"
  echo -e "$updateFileSizeEndMessage"

  if [[ $userOutputDir != "$outputDir" && -n "$(ls -A $outputDir)" ]];then
    mv $outputDir/* "$userOutputDir/" || true
    chown "$SUDO_USER":"$SUDO_USER" "$userOutputDir"/*
    echo -e "\nLogs for updating md5 values and file sizes have been moved to: $userOutputDir"
    echo -e "\nUpdate files meta data to database done!"
  else
    echo -e "\nNo logs for updating md5 values and file sizes found in: $outputDir!"
  fi

else

  # Change to gigadb-website directory
  echo -e "$updateMD5ChecksumStartMessage"
  cd ../../../../
  docker-compose run --rm files-metadata-console ./yii update/md5-values --doi="$DOI" | tee "gigadb/app/tools/readme-generator/runtime/curators/updating-md5checksum-$DOI.txt"
  echo -e "$updateMD5ChecksumEndMessage"

  echo -e "$updateFileSizeStartMessage"
  cd gigadb/app/tools/files-metadata-console
  docker-compose run --rm files-metadata-console ./yii update/file-sizes --doi="$DOI" | tee "../readme-generator/runtime/curators/updating-file-size-$DOI.txt"
  echo -e "$updateFileSizeEndMessage"
fi

