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


# generate config for Yii2 test configs in FUW webapps

SOURCE=${APP_SOURCE}/fuw/yii2-conf/common/test-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/common/config/test-local.php
VARS='$FUW_TESTDB_HOST:$FUW_TESTDB_NAME:$FUW_TESTDB_USER:$FUW_TESTDB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/fuw/yii2-conf/backend/test-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/config/test-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/fuw/yii2-conf/frontend/test-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/config/test-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/fuw/yii2-conf/console/test-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/console/config/test-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

# generate config for Codeception

SOURCE=${APP_SOURCE}/fuw/yii2-conf/backend/codeception-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/config/codeception-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

export COOKIE_RANDOM_KEY=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 16)
SOURCE=${APP_SOURCE}/fuw/yii2-conf/common/codeception-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/common/config/codeception-local.php
VARS='$COOKIE_RANDOM_KEY'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/fuw/yii2-conf/frontend/codeception-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/config/codeception-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

# generate config for Yii2 main configs in FUW webapps

SOURCE=${APP_SOURCE}/fuw/yii2-conf/common/main-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/common/config/main-local.php
VARS='$FUW_DB_HOST:$FUW_DB_NAME:$FUW_DB_USER:$FUW_DB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/fuw/yii2-conf/console/main-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/console/config/main-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

export COOKIE_RANDOM_KEY=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 16)
SOURCE=${APP_SOURCE}/fuw/yii2-conf/backend/main-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/config/main-local.php
VARS='$COOKIE_RANDOM_KEY'
envsubst $VARS < $SOURCE > $TARGET

export COOKIE_RANDOM_KEY=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 16)
SOURCE=${APP_SOURCE}/fuw/yii2-conf/frontend/main-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/config/main-local.php
VARS='$COOKIE_RANDOM_KEY'
envsubst $VARS < $SOURCE > $TARGET

# generate variable files for Yii2
SOURCE=${APP_SOURCE}/fuw/yii2-conf/common/params-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/common/config/params-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/fuw/yii2-conf/frontend/params-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/config/params-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/fuw/yii2-conf/console/params-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/console/config/params-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/fuw/yii2-conf/backend/params-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/config/params-local.php
VARS='$FUW_JWT_KEY'
envsubst $VARS < $SOURCE > $TARGET

# Configuring yii2 asset pipeline

mkdir -pv /var/www/fuw/app/backend/assets
mkdir -pv /var/www/fuw/app/frontend/assets

SOURCE=${APP_SOURCE}/fuw/yii2-conf/backend/AppAsset.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/assets/AppAsset.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/fuw/yii2-conf/frontend/AppAsset.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/assets/AppAsset.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

mkdir -pv /var/www/fuw/app/backend/web/assets
mkdir -pv /var/www/fuw/app/frontend/web/assets
chmod 0777 /var/www/fuw/app/backend/web/assets
chmod 0777 /var/www/fuw/app/frontend/web/assets
