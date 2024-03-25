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
  echo -e "Usage: ./postUpload.sh <DOI>\n"
  exit 1;
fi

updateFileSizeStartMessage="\n* About to update files' size for $DOI"
updateFileSizeEndMessage="\nDone with updating files' size for $DOI. Nb of successful changes saved in file: $outputDir/updating-file-size-$DOI.txt"

#checkValidUrlsStartMessage="\n* About to check that file urls are valid for $DOI"
#checkValidUrlsEndMessage="\nDone with checking that file urls are valid for $DOI. Invalid Urls (if any) are save in file: $outputDir/invalid-urls-$DOI.txt"

updateMD5ChecksumStartMessage="\n* About to update files' MD5 Checksum as file attribute for $DOI"
updateMD5ChecksumEndMessage="\nDone with updating files' MD5 Checksum as file attribute for $DOI. Process status is saved in file: $outputDir/updating-md5checksum-$DOI.txt"

createReadMeFileStartMessage="\n* About to create the README file for $DOI"
createReadMeFileEndMessage="\nCreated README file for $DOI."

if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile

  echo -e "$updateFileSizeStartMessage"
  docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/file-sizes --doi="$DOI" | tee "$outputDir/updating-file-size-$DOI.txt"
  echo -e "$updateFileSizeEndMessage"

#  echo -e "$checkValidUrlsStartMessage"
#  docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii check/valid-urls --doi="$DOI" | tee "$outputDir/invalid-urls-$DOI.txt"
#  echo -e "$checkValidUrlsEndMessage"

  echo -e "$updateMD5ChecksumStartMessage"
  docker run -e YII_PATH=/var/www/vendor/yiisoft/yii "registry.gitlab.com/$GITLAB_PROJECT/production_app:$GIGADB_ENV" ./protected/yiic files updateMD5FileAttributes --doi="$DOI" | tee "$outputDir/updating-md5checksum-$DOI.txt"
  echo -e "$updateMD5ChecksumEndMessage"

  if [[ $GIGADB_ENV == "staging" ]];then
    ./createReadme.sh --doi "$DOI" --outdir /app/readmeFiles --wasabi --apply
    echo -e "Created readme file and uploaded it to Wasabi gigadb-website/staging bucket directory"
  elif [[ $GIGADB_ENV == "live" ]];then
    ./createReadme.sh --doi "$DOI" --outdir /app/readmeFiles --wasabi --use-live-data --apply
    echo -e "Created readme file and uploaded it to Wasabi gigadb-website/live bucket directory"
  else
    echo -e "Environment is $GIGADB_ENV - Readme file creation is not required"
  fi

  if [[ $userOutputDir != "$outputDir" && -n "$(ls -A $outputDir)" ]];then
      mv $outputDir/* "$userOutputDir/" || true
      chown "$SUDO_USER":"$SUDO_USER" "$userOutputDir"/*
      echo -e "\nAll postUpload logs have been moved to: $userOutputDir"
      echo -e "\nPostUpload jobs done!"
  else
      echo -e "\nNo logs found in: $outputDir!"
  fi

else
  echo -e "$updateMD5ChecksumStartMessage"
  docker-compose run --rm  test ./protected/yiic files updateMD5FileAttributes --doi="$DOI" | tee "$outputDir/updating-md5checksum-$DOI.txt"
  echo -e "$updateMD5ChecksumEndMessage"

#  cd gigadb/app/tools/files-metadata-console
#  echo -e "$checkValidUrlsStartMessage"
#  docker-compose run --rm files-metadata-console ./yii check/valid-urls --doi="$DOI" | tee "$outputDir/invalid-urls-$DOI.txt"
#  echo -e "$checkValidUrlsEndMessage"

  echo -e "$updateFileSizeStartMessage"
  docker-compose run --rm files-metadata-console ./yii update/file-sizes --doi="$DOI" | tee "$outputDir/updating-file-size-$DOI.txt"
  echo -e "$updateFileSizeEndMessage"

  echo -e "$createReadMeFileStartMessage"
  cd ../readme-generator
  # Create readme file and upload to Wasabi dev directory
  ./createReadme.sh --doi "$DOI" --outdir "$outputDir" --wasabi --apply
  echo -e "$createReadMeFileEndMessage"
fi

