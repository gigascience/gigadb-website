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
APP_SOURCE=/var/www


# read env variables in same directory, from a file called .env.
# They are shared by both this script and Docker compose files.
# require gigadb's config to have run first
cd $APP_SOURCE
echo "Current working directory: $PWD"

if [ -f  ./.env ];then
    echo "An .env file is present, sourcing it"
    source "./.env"
fi


# fetch environment variables from GitLab
# Only necessary on DEV, as on CI (STG and PROD), the variables are exposed to build environment
# require gigadb's config to have run first

if [ -f  ./.secrets ];then
	echo "Sourcing secrets"
	source "./.secrets"
fi

set +a


# generate config for Yii2 test database in common project

SOURCE=${APP_SOURCE}/fuw/yii2-conf/common/test-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/common/config/test-local.php
VARS='$FUW_TESTDB_HOST:$FUW_TESTDB_NAME:$FUW_TESTDB_USER:$FUW_TESTDB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET