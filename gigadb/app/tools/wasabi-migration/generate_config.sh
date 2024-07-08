#!/usr/bin/env bash

# Bail out upon error
set -e

# Bail out if an unset variable is used
set -u

# Display lines of this script as they are executed for debugging
set -x

# Export all variables that need to be substituted in templates
set -a

# Setting up in-container application source variable (APP_SOURCE).
# It's the counterpart of the host variable APPLICATION
APP_SOURCE=/app

# Read env variables in same directory from a file called .env.
cd $APP_SOURCE
echo "Current working directory: $PWD"
if [ -f  ./.env ];then
    echo "An .env file is present, sourcing it"
    source "./.env"
fi

# Fetch and set environment variables from GitLab
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
    cat .project_vars.json | jq --arg ENVIRONMENT $GIGADB_ENV -r '.[] | select(.environment_scope == "*" or .environment_scope == "dev" ) | select(.key | test("private_key|tlsauth|ca|pem|cert";"i") | not ) |.key + "=" + .value' > .project_var

    echo "Retrieving variables from ${MISC_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${MISC_VARIABLES_URL}?per_page=100" | jq --arg ENVIRONMENT $GIGADB_ENV -r '.[] | select(.environment_scope == "*" or .environment_scope == $ENVIRONMENT ) | select(.key | test("sftp_|MATRIX_|gigadb_datasetfiles_") ) | .key + "=" + .value' > .misc_var

    # Need to account for the fact that there is no .fork_var when dealing with
    # upstream configuration. An error will be generated if we try to cat a
    # non-existent file
    if [ "$CI_PROJECT_URL" == "https://gitlab.com/gigascience/upstream/gigadb-website" ];
    then
      cat .group_var .project_var .misc_var > .secrets && rm .group_var && rm .project_var && rm .misc_var && rm .project_var_raw1 && rm .project_var_raw2 && rm .project_vars.json
    else
      # Fork configuration
      cat .group_var .fork_var .project_var .misc_var > .secrets && rm .group_var && rm .fork_var && rm .project_var && rm .misc_var && rm .project_var_raw1 && rm .project_var_raw2 && rm .project_vars.json
    fi

    echo "# Some help about this file in ops/configuration/variables/secrets-sample" >> .secrets
fi
echo "Sourcing secrets"
source "./.secrets"

# Restore default settings for variables
set +a

# Create config dir if doesn't exist
if [[ ! -d "${APP_SOURCE}/config" ]]; then
  echo "Creating directory: config"
  mkdir -p ${APP_SOURCE}/config
else
  echo "config directory already exists"
fi

# Generate rclone configuration
SOURCE=${APP_SOURCE}/rclone.conf.dist
TARGET=${APP_SOURCE}/config/rclone.conf
VARS='$WASABI_ACCESS_KEY_ID:$WASABI_SECRET_ACCESS_KEY:$gigadb_datasetfiles_aws_access_key_id:$gigadb_datasetfiles_aws_secret_access_key'
envsubst $VARS < $SOURCE > $TARGET

# Generate swatchdog configuration file
SOURCE=${APP_SOURCE}/swatchdog.conf.dist
TARGET=${APP_SOURCE}/config/swatchdog.conf
envsubst $VARS < $SOURCE > $TARGET



