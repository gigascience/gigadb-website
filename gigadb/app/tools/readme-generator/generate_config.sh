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

set +a


# generate config for Yii2 config files

SOURCE=${APP_SOURCE}/config-sources/db.php.dist
TARGET=${APP_SOURCE}/config/db.php
VARS='$GIGADB_DB:$GIGADB_USER:$GIGADB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/config-sources/test_db.php.dist
TARGET=${APP_SOURCE}/config/test_db.php
VARS='$GIGADB_DB:$GIGADB_USER:$GIGADB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET


#export TESTDB_HOST=$REVIEW_DB_HOST
#export TESTDB_PORT=$REVIEW_DB_PORT
#export TESTDB_DBNAME=${REVIEW_DB_DATABASE}_test
#export TESTDB_USER=$REVIEW_DB_USERNAME
#export TESTDB_PASSWORD=$REVIEW_DB_PASSWORD


#SOURCE=${APP_SOURCE}/config-sources/acceptance.suite.yml.template
#TARGET=${APP_SOURCE}/console/tests/acceptance.suite.yml
#VARS='$TESTDB_HOST:$TESTDB_PORT:$TESTDB_DBNAME:$TESTDB_USER:$TESTDB_PASSWORD'
#envsubst $VARS < $SOURCE > $TARGET

#SOURCE=${APP_SOURCE}/config-sources/functional.suite.yml.template
#TARGET=${APP_SOURCE}/console/tests/functional.suite.yml
#VARS='$TESTDB_HOST:$TESTDB_PORT:$TESTDB_DBNAME:$TESTDB_USER:$TESTDB_PASSWORD'
#envsubst $VARS < $SOURCE > $TARGET