#!/usr/bin/env bash
#
# Perform post upload operations to:
# 1. Create readme file
# 2. Copy readme file to user dropbox
# 3. Create doi.md5 and doi.filesizes files in user dropbox
# 4. Update database with file md5 values and file sizes

# Stop script upon error
set -e

# Allow all scripts to base themselves from directory where this postUpload.sh
# script is located
APP_SOURCE=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

PATH=/usr/local/bin:$PATH
export PATH

############################################
# Function to write error messages to STDERR
############################################
function err() {
  echo "[$(date +'%Y-%m-%dT%H:%M:%S%z')]: $*" >&2
}

#DOI=$1
#currentPath=$(pwd)
#userOutputDir="$currentPath/uploadDir"

# Parse command line parameters
while [[ $# -gt 0 ]]; do
    case "$1" in
    --doi)
        doi=$2
        shift
        ;;
    --dropbox)
        dropbox=$2
        shift
        ;;
    *)
        echo "Invalid option: $1"
        exit 1  ## Could be optional.
        ;;
    esac
    shift
done

# Check if DOI, user dropbox is not set or empty
if [ -z "$doi" ] || [ -z "$dropbox" ]; then
  if [[ $(uname -n) =~ compute ]]; then
    echo -e "Usage: /usr/local/bin/postUpload --doi <DOI> --dropbox <DROPBOX>\n"
  else
    echo -e "Usage: ./postUpload --doi <DOI> --dropbox <DROPBOX>\n"
  fi
  exit 1
fi

updateFileMetaDataStartMessage="Updating file sizes and MD5 checksums for $doi"
updateFileMetaDataEndMessage="\nDone with updating files' size and MD5 checksum for $doi."

#checkValidUrlsStartMessage="\n* About to check that file urls are valid for $DOI"
#checkValidUrlsEndMessage="\nDone with checking that file urls are valid for $DOI. Invalid Urls (if any) are save in file: $outputDir/invalid-urls-$DOI.txt"

createReadMeFileStartMessage="Creating README file for $doi"
createReadMeFileEndMessage="\nDone with creating the README file for $doi."

if [[ $(uname -n) =~ compute ]];then
  outputDir="/home/centos/uploadLogs"
  
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

  # Check user dropbox exists
  if [ ! -d "$APP_SOURCE/../../files-metadata-console/tests/_data/dropbox/$dropbox" ]; then
    err "User dropbox at gigadb-website/app/tools/files-metadata-console/tests/_data/dropbox/$dropbox does not exist"
    exit 1
  fi

  # For creating readme file on dev environment
  # /home/curators is mapped to gigadb/app/tools/readme-generator/runtime/curators directory
  outputDir="/home/curators"

  # Execute readme tool first to create readme-generator/runtime/curators
  # directory which /home/curators is mapped to
  echo -e "Creating README file for $doi"
  cd "$APP_SOURCE"/../../readme-generator
  # Create readme file and upload to Wasabi dev directory
  ./createReadme.sh --doi "$doi" --outdir "$outputDir" --wasabi --apply

  # Copy readme file to dropbox
  if [ "$outputDir" == '/home/curators' ]; then
    cp "$APP_SOURCE"/../../readme-generator/runtime/curators/readme_"$doi".txt "$APP_SOURCE/../../files-metadata-console/tests/_data/dropbox/$dropbox"
  fi

  # Create file sizes and md5 metadata files
  echo -e "Creating dataset metadata files for $doi"
  cd "${APP_SOURCE}/../../files-metadata-console/tests/_data/dropbox/$dropbox"
  docker-compose run --rm -w /gigadb/app/tools/files-metadata-console/tests/_data/dropbox/"$dropbox" files-metadata-console ../../../../scripts/md5.sh "$doi"

  echo -e "Updating file sizes and MD5 values for $doi"
  cd "$APP_SOURCE"/../../files-metadata-console/scripts
  ./filesMetaToDb.sh "$doi"

#  Skip this because it requires dataset files to be in public directory
#  echo -e "$checkValidUrlsStartMessage"
#  docker-compose run --rm files-metadata-console ./yii check/valid-urls --doi="$DOI" | tee "$outputDir/invalid-urls-$DOI.txt"
#  echo -e "$checkValidUrlsEndMessage"

fi
