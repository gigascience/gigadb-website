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
  echo "ERROR: $*" >&2
}

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
        err "Invalid option: $1"
        exit 1
        ;;
    esac
    shift
done

# Check if DOI, user dropbox is not set or empty
if [ -z "${doi}" ] || [ -z "${dropbox}" ]; then
  if [[ $(uname -n) =~ compute ]]; then
    echo -e "Usage: /usr/local/bin/postUpload --doi <DOI> --dropbox <DROPBOX>\n"
  else
    echo -e "Usage: ./postUpload --doi <DOI> --dropbox <DROPBOX>\n"
  fi
  exit 1
fi

if [[ $(uname -n) =~ compute ]]; then  # Running on staging or live environment
  outputDir="/home/centos/uploadLogs"
  
  # Source centos user's login shell settings
  . /home/centos/.bash_profile

  # Execute create readme script
  echo -e "Creating README file for ${doi}"
  if [[ ${GIGADB_ENV} == "staging" ]]; then
    /usr/local/bin/createReadme --doi "${doi}" --outdir /app/readmeFiles --wasabi --apply
    echo -e "Created readme file and uploaded it to Wasabi gigadb-website/staging bucket directory"
  elif [[ ${GIGADB_ENV} == "live" ]];then
    /usr/local/bin/createReadme --doi "${doi}" --outdir /app/readmeFiles --wasabi --use-live-data --apply
    echo -e "Created readme file and uploaded it to Wasabi gigadb-website/live bucket directory"
  else
    echo -e "Environment is ${GIGADB_ENV} - Readme file creation is not required"
  fi

  # Stop execution if readme file does not exist
  if [ ! -f "/home/centos/readmeFiles/readme_${doi}.txt" ]; then
    err "readme_${doi}.txt was not created"
    exit 1
  fi

  echo -e "Copying README file into dropbox ${dropbox}"
  cp "/home/centos/readmeFiles/readme_${doi}.txt" "/share/dropbox/${dropbox}"
  
  # Create file sizes and md5 metadata files
  echo -e "Creating dataset metadata files for ${doi}"
  cd "/share/dropbox/${dropbox}"
  sudo /usr/local/bin/calculateChecksumSizes "${doi}"

  echo -e "Updating file sizes and MD5 values in database for ${doi}"
  /usr/local/bin/filesMetaToDb "${doi}"

#  Skip this because it requires dataset files to be in public directory
#  echo -e "Checking file urls are valid for $DOI"
#  docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:${GIGADB_ENV}" ./yii check/valid-urls --doi="$DOI" | tee "${outputDir}/invalid-urls-$DOI.txt"
#  echo -e "Finished checking file urls are valid for $DOI. Invalid Urls (if any) are save in file: ${outputDir}/invalid-urls-$DOI.txt"

else  # Running on dev environment

  # Check user dropbox exists
  if [ ! -d "${APP_SOURCE}/../../files-metadata-console/tests/_data/dropbox/${dropbox}" ]; then
    err "User dropbox at gigadb-website/app/tools/files-metadata-console/tests/_data/dropbox/${dropbox} does not exist"
    exit 1
  fi

  # For creating readme file on dev environment
  # /home/curators is mapped to gigadb/app/tools/readme-generator/runtime/curators directory
  outputDir="/home/curators"

  # Execute readme tool first to create readme-generator/runtime/curators
  # directory which /home/curators is mapped to
  echo -e "Creating README file for ${doi}"
  cd "${APP_SOURCE}/../../readme-generator"
  # Create readme file and upload to Wasabi dev directory
  ./createReadme.sh --doi "${doi}" --outdir "${outputDir}" --wasabi --apply
  
  # Stop execution if readme file does not exist
  if [ ! -f "${APP_SOURCE}/../../readme-generator/runtime/curators/readme_${doi}.txt" ]; then
    err "readme_${doi}.txt was not created"
    exit 1
  fi

  # Copy readme file to dropbox
  echo -e "Copying README file into dropbox ${dropbox}"
  if [ "${outputDir}" == '/home/curators' ]; then
    cp "${APP_SOURCE}/../../readme-generator/runtime/curators/readme_${doi}.txt" "${APP_SOURCE}/../../files-metadata-console/tests/_data/dropbox/${dropbox}"
  fi

  # Create file sizes and md5 metadata files
  echo -e "Creating dataset metadata files for ${doi}"
  cd "${APP_SOURCE}/../../files-metadata-console/tests/_data/dropbox/${dropbox}"
  docker-compose run --rm -w /gigadb/app/tools/files-metadata-console/tests/_data/dropbox/"${dropbox}" files-metadata-console ../../../../scripts/md5.sh "$doi"

  echo -e "Updating file sizes and MD5 values in database for ${doi}"
  cd "${APP_SOURCE}/../../files-metadata-console/scripts"
  ./filesMetaToDb.sh "${doi}"

#  Skip this because it requires dataset files to be in public directory
#  echo -e "Checking file urls are valid for $DOI"
#  docker-compose run --rm files-metadata-console ./yii check/valid-urls --doi="$DOI" | tee "${outputDir}/invalid-urls-$DOI.txt"
#  echo -e "Finished checking file urls are valid for $DOI. Invalid Urls (if any) are save in file: ${outputDir}/invalid-urls-$DOI.txt"

fi
