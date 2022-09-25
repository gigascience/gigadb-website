#!/usr/bin/env bash

# bail out upon error
set -e

# bail out if an unset variable is used
set -u

# display the lines of this script as they are executed for debugging
set -x

# export all variables that need to be substitued in templates
set -a
# Setting up in-container application source variable (APP_SOURCE).
# It's the counterpart of the host variable APPLICATION
APP_SOURCE=/app


# read env variables in same directory, from a file called .env.
# They are shared by both this script and Docker compose files.
# require gigadb's config to have run first
cd $APP_SOURCE
echo "Current working directory: $PWD"

if [ -f  ./.env ];then
    echo "An .env file is present, sourcing it"
    source "./.env"
fi


echo "Generating configuration for environment: $GIGADB_ENV"

# fetch and set environment variables from GitLab
# Only necessary on DEV, as on CI (STG and PROD), the variables are exposed to build environment

if [[ -z $GITLAB_PRIVATE_TOKEN ]];then
  echo "GITLAB_PRIVATE_TOKEN is not defined!"
fi

if [[ -z $MISC_VARIABLES_URL ]];then
  echo "MISC_VARIABLES_URL is not defined!"
fi

if [[ -z $GIGADB_ENV ]];then
  echo "GIGADB_ENV is not defined!"
fi

if ! [ -s ./.secrets ];then

    # deal with special case we are in Upstream
    if [[ $CI_PROJECT_URL == "https://gitlab.com/gigascience/upstream/gigadb-website" ]];then
      PROJECT_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fupstream%2F$REPO_NAME/variables"
    fi

    echo "Retrieving variables from ${PROJECT_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${PROJECT_VARIABLES_URL}?per_page=100&page=1"  > .project_var_raw1
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${PROJECT_VARIABLES_URL}?per_page=100&page=2"  > .project_var_raw2
    jq -s 'add' .project_var_raw1 .project_var_raw2 > .project_vars.json
    cat .project_vars.json | jq --arg ENVIRONMENT "$GIGADB_ENV" -r '.[] | select( .environment_scope == $ENVIRONMENT ) | select(.key | test("gigadb_db_") ) |.key + "=" + .value' > .project_var

    echo "Retrieving variables from ${MISC_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${MISC_VARIABLES_URL}?per_page=100" | jq --arg ENVIRONMENT "$GIGADB_ENV" -r '.[] | select(.environment_scope == "*" or .environment_scope == $ENVIRONMENT ) | select(.key | test("_ftp_") ) | .key + "=" + .value' > .misc_var

    # Create .secrets from the multiple parts
    cat .project_var .misc_var > .secrets #&& rm .project_var && rm .misc_var && rm .project_var_raw1 && rm .project_var_raw2 && rm .project_vars.json
fi
echo "Sourcing secrets"
source "./.secrets"

set +a


# generate config for Yii2 config files

if [[ $GIGADB_ENV == "dev" ]];then
  export legacy_host=pg9_3
else
  export legacy_host=host.docker.internal
fi

SOURCE=${APP_SOURCE}/config/params.php.dist
TARGET=${APP_SOURCE}/config/params.php
VARS='$legacy_host:$cngbbackup_ftp_hostname:$cngbbackup_ftp_username:$cngbbackup_ftp_password'
envsubst $VARS < $SOURCE > $TARGET
envsubst $VARS < $SOURCE > $TARGET