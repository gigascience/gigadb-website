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
      PROJECT_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fupstream%2Fgigadb-website/variables"
    fi

    echo "Retrieving variables from ${GROUP_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${GROUP_VARIABLES_URL}" | jq -r '.[] | select(.key != "ANALYTICS_PRIVATE_KEY") | .key + "=" + .value' > .group_var

    if [[ $CI_PROJECT_URL != "https://gitlab.com/gigascience/upstream/gigadb-website" ]];then
      echo "Retrieving variables from ${FORK_VARIABLES_URL}"
      curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${FORK_VARIABLES_URL}?per_page=100" | jq -r '.[] | select(.key != "ANALYTICS_PRIVATE_KEY") | .key + "=" + .value' > .fork_var
    fi

    echo "Retrieving variables from ${PROJECT_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${PROJECT_VARIABLES_URL}?per_page=100&page=1"  > .project_var_raw1
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${PROJECT_VARIABLES_URL}?per_page=100&page=2"  > .project_var_raw2
    jq -s 'add' .project_var_raw1 .project_var_raw2 > .project_vars.json
    cat .project_vars.json | jq --arg ENVIRONMENT $GIGADB_ENV -r '.[] | select( .environment_scope == $ENVIRONMENT or .environment_scope == "*") | select(.key | test("key|ca|pem|cert";"i") | not ) |.key + "=" + .value' > .project_var

    echo "Retrieving variables from ${MISC_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${MISC_VARIABLES_URL}?per_page=100" | jq --arg ENVIRONMENT $GIGADB_ENV -r '.[] | select(.environment_scope == "*" or .environment_scope == $ENVIRONMENT ) | select(.key | test("sftp_") ) | .key + "=" + .value' > .misc_var

    cat .group_var .fork_var .project_var .misc_var > .secrets && rm .group_var && rm .fork_var && rm .project_var && rm .misc_var && rm .project_var_raw1 && rm .project_var_raw2 && rm .project_vars.json
    echo "# Some help about this file in ops/configuration/variables/secrets-sample" >> .secrets
fi
echo "Sourcing secrets"
source "./.secrets"

# If we are on staging environment override variable name with their remote environment counterpart
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
    GIGADB_HOST=$gigadb_db_host
    GIGADB_USER=$gigadb_db_user
    GIGADB_PASSWORD=$gigadb_db_password
    GIGADB_DB=$gigadb_db_database
    echo "GigaDB_ENV is not dev nor CI!!"
    echo $GIGADB_HOST
fi

# restore default settings for variables
set +a

# generate config for Yii2 config files

SOURCE=${APP_SOURCE}/config-sources/db.php.dist
TARGET=${APP_SOURCE}/config/db.php
VARS='$GIGADB_HOST:$GIGADB_DB:$GIGADB_USER:$GIGADB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/config-sources/test_db.php.dist
TARGET=${APP_SOURCE}/config/test_db.php
VARS='$GIGADB_HOST:$GIGADB_DB:$GIGADB_USER:$GIGADB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

# Create curators directory in runtime folder
mkdir -p ./runtime/curators