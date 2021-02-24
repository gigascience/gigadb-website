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

# If we are on staging environment override variable name with STAGING_* or REMOTE_* counterpart
export DEBUG=true
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
    FUW_DB_HOST=$REMOTE_FUW_DB_HOST
    FUW_DB_USER=$REMOTE_FUW_DB_USER
    FUW_DB_PASSWORD=$REMOTE_FUW_DB_PASSWORD
    FUW_DB_NAME=$REMOTE_FUW_DB_NAME
    DEBUG=false
    HOME_URL=$REMOTE_HOME_URL
    SERVER_HOSTNAME=$REMOTE_HOSTNAME
fi

# generate config for Yii2 test configs in FUW webapps

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/common/test-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/common/config/test-local.php
VARS='$FUW_TESTDB_HOST:$FUW_TESTDB_NAME:$FUW_TESTDB_USER:$FUW_TESTDB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/backend/test-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/config/test-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/frontend/test-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/config/test-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/console/test-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/console/config/test-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

# generate config for Codeception

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/backend/codeception-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/config/codeception-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

export COOKIE_RANDOM_KEY=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 16)
SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/common/codeception-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/common/config/codeception-local.php
VARS='$COOKIE_RANDOM_KEY'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/frontend/codeception-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/config/codeception-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

# generate config for Yii2 main configs in FUW webapps
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/common/production/main-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/common/config/main-local.php
VARS='$FUW_DB_HOST:$FUW_DB_NAME:$FUW_DB_USER:$FUW_DB_PASSWORD:$REMOTE_SMTP_HOST:$REMOTE_SMTP_PORT:$REMOTE_SMTP_USERNAME:$REMOTE_SMTP_PASSWORD'
else
    SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/common/main-local.php.dist
    TARGET=${APP_SOURCE}/fuw/app/common/config/main-local.php
    VARS='$FUW_DB_HOST:$FUW_DB_NAME:$FUW_DB_USER:$FUW_DB_PASSWORD'
fi
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/console/main-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/console/config/main-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

export COOKIE_RANDOM_KEY=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 16)
SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/backend/main-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/config/main-local.php
VARS='$COOKIE_RANDOM_KEY'
envsubst $VARS < $SOURCE > $TARGET

export COOKIE_RANDOM_KEY=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 16)
SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/frontend/main-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/config/main-local.php
VARS='$COOKIE_RANDOM_KEY'
envsubst $VARS < $SOURCE > $TARGET

# generate variable files for Yii2
set +e
default_route_via_host=$(/sbin/ip route|awk '/default/ { print $3 }')
nc -zv host.docker.internal 2375 2> /dev/null
name_is_host_docker=$?
if [ $name_is_host_docker -eq 0 ];
then
	REMOTE_DOCKER_HOSTNAME="tcp://host.docker.internal:2375"
else
	REMOTE_DOCKER_HOSTNAME="tcp://$default_route_via_host:2375"
fi
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
    REMOTE_DOCKER_HOSTNAME=tcp://$remote_public_ip:2376
    cafile="/etc/certs/ca.pem"
    local_cert="/etc/certs/cert.pem"
    local_pk="/etc/certs/key.pem"
    peer_name=$remote_public_ip
fi
export REMOTE_DOCKER_HOSTNAME
echo "Writing REMOTE_DOCKER_HOSTNAME to params-local as '$REMOTE_DOCKER_HOSTNAME'"
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
    export $cafile
    export $local_cert
    export $local_pk
    export $peer_name
    SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/common/production/params-local.php.dist
    VARS='$FUW_JWT_KEY:$REMOTE_DOCKER_HOSTNAME:$cafile:$local_cert:$local_pk:$peer_name:$REMOTE_HOSTNAME:$HOME_URL'
else
    SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/common/params-local.php.dist
    VARS='$FUW_JWT_KEY:$REMOTE_DOCKER_HOSTNAME:$SERVER_HOSTNAME:$HOME_URL'
fi
TARGET=${APP_SOURCE}/fuw/app/common/config/params-local.php
envsubst $VARS < $SOURCE > $TARGET
if [[ $GIGADB_ENV == "CI" ]];then
    echo "REMOTE_DOCKER_HOSTNAME=$REMOTE_DOCKER_HOSTNAME" >> ./.env
fi
set -e

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/frontend/params-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/config/params-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/console/params-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/console/config/params-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/backend/params-local.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/config/params-local.php
VARS=''
envsubst $VARS < $SOURCE > $TARGET

# Configuring yii2 asset pipeline

mkdir -pv /var/www/fuw/app/backend/assets
mkdir -pv /var/www/fuw/app/frontend/assets

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/backend/AppAsset.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/assets/AppAsset.php

cp $SOURCE $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/frontend/AppAsset.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/assets/AppAsset.php

cp $SOURCE $TARGET

mkdir -pv /var/www/fuw/app/backend/web/assets
mkdir -pv /var/www/fuw/app/frontend/web/assets
chmod 0777 /var/www/fuw/app/backend/web/assets
chmod 0777 /var/www/fuw/app/frontend/web/assets

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/frontend/index.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/web/index.php
VARS='$GIGADB_ENV'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/frontend/index-test.php.dist
TARGET=${APP_SOURCE}/fuw/app/frontend/web/index-test.php
VARS='$GIGADB_ENV'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/backend/index.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/web/index.php
VARS='$DEBUG:$GIGADB_ENV'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/backend/index-test.php.dist
TARGET=${APP_SOURCE}/fuw/app/backend/web/index-test.php
VARS='$GIGADB_ENV'
envsubst $VARS < $SOURCE > $TARGET

# Configuring Watcher's PHP script to allow database access

SOURCE=${APP_SOURCE}/fuw/watcher/conf/watcherconfig.ini.dist
TARGET=${APP_SOURCE}/fuw/watcher/conf/watcherconfig.ini
VARS='$FUW_DB_HOST:$FUW_DB_NAME:$FUW_DB_USER:$FUW_DB_PASSWORD:$HOME_URL'
envsubst $VARS < $SOURCE > $TARGET
