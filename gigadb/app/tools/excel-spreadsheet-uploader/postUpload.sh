#!/usr/bin/env bash

set -e

# Display the help message
display_help() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --doi <value>           Specify the DOI value"
    echo "  --wasabi                (Optional) Copy the readme file to wasabi bucket in dry-run mode"
    echo "  --apply                 (Optional) Copy the readme file to wasabi non live bucket"
    echo "  --use-live-data         (Optional) Copy the readme file to wasabi live bucket"
    echo "  -h, --help              Display this help message"
    echo ""
}

PATH=/usr/local/bin:$PATH
export PATH

# Prepare options for createReadme.sh command
options=()

while [[ $# -gt 0 ]]; do
  case "$1" in
    --doi)
      DOI=$2
      shift
      ;;
    --wasabi)
      options+=("--wasabi")
      ;;
    --apply)
      options+=("--apply")
      ;;
    --use-live-data)
      options+=("--use-live-data")
      ;;
    -h|--help)
      display_help
      exit 0
      ;;
    *)
      echo "Invalid option: $1"
      exit 1  ## Could be optional.
      ;;
  esac
  shift
done

currentPath=$(pwd)
userOutputDir="$currentPath/uploadDir"

if [[ $(uname -n) =~ compute ]];then
  outputDir="/home/centos/uploadLogs"
else
  outputDir="/tmp"
fi

if [ -z "$DOI" ];then
  echo -e "Usage: ./postUpload.sh <DOI>\n"
  exit 1;
fi

updateFileSizeStartMessage="\n* About to update files' size for $DOI"
updateFileSizeEndMessage="\nDone with updating files' size for $DOI. Nb of successful changes saved in file: $outputDir/updating-file-size-$DOI.txt"

checkValidUrlsStartMessage="\n* About to check that file urls are valid for $DOI"
checkValidUrlsEndMessage="\nDone with checking that file urls are valid for $DOI. Invalid Urls (if any) are save in file: $outputDir/invalid-urls-$DOI.txt"

updateMD5ChecksumStartMessage="\n* About to update files' MD5 Checksum as file attribute for $DOI"
updateMD5ChecksumEndMessage="\nDone with updating files' MD5 Checksum as file attribute for $DOI. Process status is saved in file: $outputDir/updating-md5checksum-$DOI.txt"

createReadMeFileStartMessage="\n* About to create the README file for $DOI"
createReadMeFileEndMessage="\nDone with creating the README file for $DOI. The README file is saved in file: $outputDir/readme-$DOI.txt"

if [[ $(uname -n) =~ compute ]];then
  . /home/centos/.bash_profile

  echo -e "$updateFileSizeStartMessage"
  docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/file-size --doi="$DOI" | tee "$outputDir/updating-file-size-$DOI.txt"
  echo -e "$updateFileSizeEndMessage"

  echo -e "$checkValidUrlsStartMessage"
  docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii check/valid-urls --doi="$DOI" | tee "$outputDir/invalid-urls-$DOI.txt"
  echo -e "$checkValidUrlsEndMessage"

  echo -e "$updateMD5ChecksumStartMessage"
  docker run -e YII_PATH=/var/www/vendor/yiisoft/yii "registry.gitlab.com/$GITLAB_PROJECT/production_app:$GIGADB_ENV" ./protected/yiic files updateMD5FileAttributes --doi="$DOI" | tee "$outputDir/updating-md5checksum-$DOI.txt"
  echo -e "$updateMD5ChecksumEndMessage"

  echo -e "$createReadMeFileStartMessage"
  sudo /home/centos/createReadme.sh --doi "$DOI" --outdir /app/readmeFiles "${options[@]}" | tee "$outputDir/readme-$DOI.txt"
  echo -e "$createReadMeFileEndMessage"

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

  cd gigadb/app/tools/files-metadata-console
  echo -e "$checkValidUrlsStartMessage"
  docker-compose run --rm files-metadata-console ./yii check/valid-urls --doi="$DOI" | tee "$outputDir/invalid-urls-$DOI.txt"
  echo -e "$checkValidUrlsEndMessage"

  echo -e "$updateFileSizeStartMessage"
  docker-compose run --rm files-metadata-console ./yii update/file-size --doi="$DOI" | tee "$outputDir/updating-file-size-$DOI.txt"
  echo -e "$updateFileSizeEndMessage"

  echo -e "$createReadMeFileStartMessage"
  cd ../readme-generator
  ./createReadme.sh --doi "$DOI" --outdir /home/curators "${options[@]}" | tee "$outputDir/readme-$DOI.txt"
  echo -e "$createReadMeFileEndMessage"
fi

