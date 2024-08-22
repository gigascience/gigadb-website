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

# Check if DOI is not set or empty
if [[ -z "$DOI" ]]; then
  if [[ $(uname -n) =~ compute ]]; then
    echo -e "Usage: /usr/local/bin/postUpload <DOI>\n"
  else
    echo -e "Usage: ./postUpload <DOI>\n"
  fi
  exit 1
fi

updateFileMetaDataStartMessage="\n* About to update files' size and MD5 checksum for $DOI"
updateFileMetaDataEndMessage="\nDone with updating files' size and MD5 checksum for $DOI."

#checkValidUrlsStartMessage="\n* About to check that file urls are valid for $DOI"
#checkValidUrlsEndMessage="\nDone with checking that file urls are valid for $DOI. Invalid Urls (if any) are save in file: $outputDir/invalid-urls-$DOI.txt"

createReadMeFileStartMessage="\n* About to create the README file for $DOI"
createReadMeFileEndMessage="\nDone with creating the README file for $DOI."

if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile

# Execute create readme script
  echo -e "$createReadMeFileStartMessage"
  if [[ $GIGADB_ENV == "staging" ]];then
    /usr/local/bin/createReadme --doi "$DOI" --outdir /app/readmeFiles --wasabi --apply
    echo -e "Created readme file and uploaded it to Wasabi gigadb-website/staging bucket directory"
  elif [[ $GIGADB_ENV == "live" ]];then
    /usr/local/bin/createReadme --doi "$DOI" --outdir /app/readmeFiles --wasabi --use-live-data --apply
    echo -e "Created readme file and uploaded it to Wasabi gigadb-website/live bucket directory"
  else
    echo -e "Environment is $GIGADB_ENV - Readme file creation is not required"
  fi
  echo -e "$createReadMeFileEndMessage"

#  Skip this because it requires dataset files to be in public directory
#  echo -e "$checkValidUrlsStartMessage"
#  docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii check/valid-urls --doi="$DOI" | tee "$outputDir/invalid-urls-$DOI.txt"
#  echo -e "$checkValidUrlsEndMessage"

#  Execute the filesMetaDb script to  update md5 values and file sizes to db
  echo -e "$updateFileMetaDataStartMessage"
  /usr/local/bin/filesMetaToDb "$DOI"
  echo -e "$updateFileMetaDataEndMessage"

else  # Running on dev environment

  # Execute readme tool first to create readme-generator/runtime/curators
  # directory which /home/curators is mapped to
  echo -e "$createReadMeFileStartMessage"
  cd ../../readme-generator
  # Create readme file and upload to Wasabi dev directory
  ./createReadme.sh --doi "$DOI" --outdir "$outputDir" --wasabi --apply
  echo -e "$createReadMeFileEndMessage"

  echo -e "$updateFileMetaDataStartMessage"
  cd ../files-metadata-console/scripts
  ./filesMetaToDb.sh "$DOI"
  echo -e "$updateFileMetaDataEndMessage"

#  Skip this because it requires dataset files to be in public directory
#  echo -e "$checkValidUrlsStartMessage"
#  docker-compose run --rm files-metadata-console ./yii check/valid-urls --doi="$DOI" | tee "$outputDir/invalid-urls-$DOI.txt"
#  echo -e "$checkValidUrlsEndMessage"

fi
