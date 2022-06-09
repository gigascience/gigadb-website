#!/usr/bin/env bash

# bail out upon error
set -e

# bail out if an unset variable is used
set -u

# display the lines of this script as they are executed for debugging
# set -x

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


# fetch and set environment variables from GitLab
# Only necessary on DEV, as on CI (STG and PROD), the variables are exposed to build environment

if ! [ -f  ./.secrets ];then
    echo "Retrieving variables from ${GROUP_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${GROUP_VARIABLES_URL}" | jq -r '.[] | select(.key != "ANALYTICS_PRIVATE_KEY") | .key + "=" + .value' > .group_var

    echo "Retrieving variables from ${FORK_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${FORK_VARIABLES_URL}?per_page=100" | jq -r '.[] | select(.key != "ANALYTICS_PRIVATE_KEY") |.key + "=" + .value' > .fork_var

    echo "Retrieving variables from ${PROJECT_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${PROJECT_VARIABLES_URL}?per_page=100" | jq -r '.[] | select(.environment_scope == "*" or .environment_scope == "dev" ) | select(.key != "ANALYTICS_PRIVATE_KEY") | select(.key != "TLSAUTH_CERT") | select(.key != "TLSAUTH_KEY") | select(.key != "TLSAUTH_CA") | select(.key != "docker_tlsauth_ca") | select(.key != "docker_tlsauth_key") | select(.key != "docker_tlsauth_cert") | select(.key != "tls_fullchain_pem") | select(.key != "tls_privkey_pem") | select(.key != "tls_chain_pem") |.key + "=" + .value' > .project_var

    echo "Retrieving variables from ${MISC_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${MISC_VARIABLES_URL}?per_page=100" | jq -r '.[] | select(.environment_scope == "*" or .environment_scope == "dev" ) | select(.key | test("sftp_") ) | .key + "=" + .value' > .misc_var


    cat .group_var .fork_var .project_var .misc_var > .secrets && rm .group_var && rm .fork_var && rm .project_var && rm .misc_var
    echo "# Some help about this file in ops/configuration/variables/secrets-sample" >> .secrets
fi
echo "Sourcing secrets"
source "./.secrets"

set +a


# generate config for Yii2 config files

SOURCE=${APP_SOURCE}/config-sources/params-local.php.template
TARGET=${APP_SOURCE}/environments/$REVIEW_ENV/common/config/params-local.php
VARS='$REVIEW_DB_HOST:$REVIEW_DB_PORT:$REVIEW_DB_DATABASE:$REVIEW_DB_USERNAME:$REVIEW_DB_PASSWORD:$sftp_hostname:$sftp_username:$sftp_password:$sftp_directory'
envsubst $VARS < $SOURCE > $TARGET

