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
    echo "Retrieving variables from ${MISC_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${MISC_VARIABLES_URL}?per_page=100" | jq --arg ENVIRONMENT "$GIGADB_ENV" -r '.[] | select(.environment_scope == "*" or .environment_scope == "dev" ) | select(.key | test("_ftp_") ) | .key + "=" + .value' > .secrets
fi
echo "Sourcing secrets"
source "./.secrets"

set +a


# generate config for Yii2 config files

SOURCE=${APP_SOURCE}/config/params.php.dist
TARGET=${APP_SOURCE}/config/params.php
VARS='$cngbbackup_ftp_hostname:$cngbbackup_ftp_username:$cngbbackup_ftp_password'
envsubst $VARS < $SOURCE > $TARGET
envsubst $VARS < $SOURCE > $TARGET