#!/usr/bin/env bash

set -e

PATH=/usr/local/bin:$PATH
export PATH

# Where filesMetaToDb.sh script is located
APP_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# doi.filesizes and doi.md5 located in current working directory will be used by
# tool to update dataset file metadata 
WORKING_DIR=$(pwd)

DOI=$1
if [ -z "${DOI}" ];then
  if [[ $(uname -n) =~ compute ]]; then
    echo -e "Usage: /usr/local/bin/filesMetaToDb <DOI>\n"
  else
    echo -e "Usage: ${APP_DIR}/filesMetaToDb.sh <DOI>\n "
  fi
  exit 1;
fi

#######################################
# Check doi.md5 and doi.filesizes files
# exist in current working directory
# Globals:
#   WORKING_DIR
# Arguments:
#   None
#######################################
function check_files_exist() {
  # Check current directory contains doi.md and doi.filesizes
  if ! test -f "${WORKING_DIR}/${DOI}.md"; then
    err "A ${DOI}.md file is required in this directory"
    exit 1
  fi
  if ! test -f "${WORKING_DIR}/${DOI}.filesizes"; then
    err "A ${DOI}.filesizes file is required in this directory"
    exit 1
  fi
}

if [[ $(uname -n) =~ compute ]]; then
  . /home/centos/.bash_profile
  
  # Check current directory is a user dropbox
  if [[ ! "${WORKING_DIR}" == *"/share/dropbox/user"* ]]; then
    err "filesMetaToDb script should only be used in a user directory located at /share/dropbox"
    exit 1
  fi

  check_files_exist

  echo -e "Updating md5 checksum values as file attributes for ${DOI}"
  docker run --rm -v "${WORKING_DIR}":/gigadb/app/tools/files-metadata-console/metadata "registry.gitlab.com/${GITLAB_PROJECT}/production-files-metadata-console:${GIGADB_ENV}" ./yii update/md5-values --doi="${DOI}"
  echo -e "Updating file sizes for ${DOI}"
  docker run --rm -v "${WORKING_DIR}":/gigadb/app/tools/files-metadata-console/metadata "registry.gitlab.com/${GITLAB_PROJECT}/production-files-metadata-console:${GIGADB_ENV}" ./yii update/file-sizes --doi="${DOI}"

else
  # Check developer is at gigadb-website/gigadb/app/tools/files-metadata-console/tests/_data/dropbox/user
  if [[ ! "${WORKING_DIR}" == *"gigadb-website/gigadb/app/tools/files-metadata-console/tests/_data/dropbox/user"* ]]; then
    err "filesMetaToDb.sh script should only be used in a files-metadata-console/tests/_data/dropbox/user* directory in dev environment"
    exit 1
  fi

  check_files_exist

  echo -e "Updating md5 checksum values as file attributes for ${DOI}"
  docker-compose run --rm -v "${WORKING_DIR}":/gigadb/app/tools/files-metadata-console/metadata files-metadata-console ./yii update/md5-values --doi="${DOI}"
  echo -e "Updating file sizes for $DOI"
  docker-compose run --rm -v "${WORKING_DIR}":/gigadb/app/tools/files-metadata-console/metadata files-metadata-console ./yii update/file-sizes --doi="${DOI}"
fi

echo -e "Updated file metadata for ${DOI} in database"
