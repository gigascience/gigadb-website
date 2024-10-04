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

# doi.filesizes and doi.md5 located in current working directory will be used by
# tool to update dataset file metadata 
WORKING_DIR=$(pwd)

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
  
  # Source centos user's login shell settings
  . /home/centos/.bash_profile

  # Execute create readme script
  echo -e "Creating README file for ${doi}"
  if [[ "${GIGADB_ENV}" == "staging" ]]; then
    "${WORKING_DIR}"../../../usr/local/bin/createReadme --doi "${doi}" --wasabi --apply
    echo -e "Created readme file and uploaded it to Wasabi gigadb-website/staging bucket directory"
  elif [[ "${GIGADB_ENV}" == "live" ]];then
    "${WORKING_DIR}"../../../usr/local/bin/createReadme --doi "${doi}" --wasabi --use-live-data --apply
    echo -e "Created readme file and uploaded it to Wasabi gigadb-website/live bucket directory"
  else
    echo -e "Environment is ${GIGADB_ENV} - Readme file creation is not required"
  fi

  # Create file sizes and md5 metadata files
  echo -e "Creating dataset metadata files for ${doi}"
  cd "/share/dropbox/${dropbox}"
  "${WORKING_DIR}"/../../../usr/local/bin/calculateChecksumSizes "${doi}"

  echo -e "Updating file sizes and MD5 values in database for ${doi}"
  "${WORKING_DIR}"/../../../usr/local/bin/filesMetaToDb "${doi}"

#  Skip this because it requires dataset files to be in public directory
#  echo -e "Checking file urls are valid for $DOI"
#  docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:${GIGADB_ENV}" ./yii check/valid-urls --doi="$DOI" | tee "${outputDir}/invalid-urls-$DOI.txt"
#  echo -e "Finished checking file urls are valid for $DOI. Invalid Urls (if any) are save in file: ${outputDir}/invalid-urls-$DOI.txt"

else  # Running on dev environment

  # Check user dropbox exists
  if [ ! -d "${APP_SOURCE}"/../../files-metadata-console/tests/_data/dropbox/"${dropbox}" ]; then
    err "User dropbox at gigadb-website/app/tools/files-metadata-console/tests/_data/dropbox/${dropbox} does not exist"
    exit 1
  fi

  echo -e "Creating README file for ${doi}"
  "${WORKING_DIR}"/../../../../../readme-generator/createReadme.sh --doi "${doi}" --wasabi --apply
  # Stop execution if readme file does not exist
  if [ ! -f "${WORKING_DIR}/readme_${doi}.txt" ]; then
    err "readme_${doi}.txt was not created"
    exit 1
  fi

  # Create file sizes and md5 metadata files
  echo -e "Creating dataset metadata files for ${doi}"
  docker-compose run --rm -w /gigadb/app/tools/files-metadata-console/tests/_data/dropbox/"${dropbox}" files-metadata-console ../../../../scripts/md5.sh "$doi"

  echo -e "Updating file sizes and MD5 values in database for ${doi}"
  "${WORKING_DIR}"/../../../../scripts/filesMetaToDb.sh "${doi}"

#  Skip this because it requires dataset files to be in public directory
#  echo -e "Checking file urls are valid for $DOI"
#  docker-compose run --rm files-metadata-console ./yii check/valid-urls --doi="$DOI" | tee "${outputDir}/invalid-urls-$DOI.txt"
#  echo -e "Finished checking file urls are valid for $DOI. Invalid Urls (if any) are save in file: ${outputDir}/invalid-urls-$DOI.txt"

fi
