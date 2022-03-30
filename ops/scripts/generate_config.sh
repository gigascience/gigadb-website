#!/usr/bin/env bash

# bail out upon error
set -e


# display the lines of this script as they are executed for debugging
#set -x

# export all variables that need to be substitued in templates
set -a
# Setting up in-container application source variable (APP_SOURCE).
# It's the counterpart of the host variable APPLICATION
APP_SOURCE=/var/www

# Warning to dissuade from modify the generated composer.json file
COMPOSER_WARNING="!! Auto-generated file, edit ops/php-conf/composer.json.dist instead"

# read env variables in same directory, from a file called .env.
# They are shared by both this script and Docker compose files.
cd $APP_SOURCE
echo "Current working directory: $PWD"

if [ -f  ./.env ];then
    echo "An .env file is present, sourcing it"
    source "./.env"
fi



# Print directory of this script. We will need it to find nginx config
THIS_SCRIPT_DIR=`dirname "$BASH_SOURCE"`
echo "Running ${THIS_SCRIPT_DIR}/generate_config.sh for environment: $GIGADB_ENV"


# fetch and set environment variables from GitLab
# Only necessary on DEV, as on CI (STG and PROD), the variables are exposed to build environment

if ! [ -f  ./.secrets ];then
    echo "Retrieving variables from ${GROUP_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${GROUP_VARIABLES_URL}" | jq -r '.[] | select(.key != "ANALYTICS_PRIVATE_KEY") | .key + "=" + .value' > .group_var

    echo "Retrieving variables from ${FORK_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${FORK_VARIABLES_URL}?per_page=100" | jq -r '.[] | select(.key != "ANALYTICS_PRIVATE_KEY") |.key + "=" + .value' > .fork_var

    echo "Retrieving variables from ${PROJECT_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${PROJECT_VARIABLES_URL}?per_page=100" | jq -r '.[] | select(.environment_scope == "*" or .environment_scope == "dev" ) | select(.key != "ANALYTICS_PRIVATE_KEY") | select(.key != "TLSAUTH_CERT") | select(.key != "TLSAUTH_KEY") | select(.key != "TLSAUTH_CA") | select(.key != "docker_tlsauth_ca") | select(.key != "docker_tlsauth_key") | select(.key != "docker_tlsauth_cert") | select(.key != "tls_fullchain_pem") | select(.key != "tls_privkey_pem") | select(.key != "tls_chain_pem") |.key + "=" + .value' > .project_var
    cat .group_var .fork_var .project_var > .secrets && rm .group_var && rm .fork_var && rm .project_var
    echo "# Some help about this file in ops/configuration/variables/secrets-sample" >> .secrets
fi
echo "Sourcing secrets"
source "./.secrets"

## Deal with PHP memory limits
export PHP_MEM=-1

# If we are on staging environment override variable name with their remote environment counterpart
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
    GIGADB_HOST=$gigadb_db_host
    GIGADB_USER=$gigadb_db_user
    GIGADB_PASSWORD=$gigadb_db_password
    GIGADB_DB=$gigadb_db_database
    HOME_URL=$REMOTE_HOME_URL
    PUBLIC_HTTP_PORT=$REMOTE_PUBLIC_HTTP_PORT
    PUBLIC_HTTPS_PORT=$REMOTE_PUBLIC_HTTPS_PORT
    SERVER_HOSTNAME=$REMOTE_HOSTNAME
    PHP_MEM=512M
fi

# restore default settings for variables
set +a


# do the stuff that vagrant would normally do. Even if vagrant is used, doing this stuff regardless is still ok.
mkdir -p ${APP_SOURCE}/protected/runtime && chmod 777 ${APP_SOURCE}/protected/runtime
mkdir -p ${APP_SOURCE}/protected/runtime/mail && chmod 777 ${APP_SOURCE}/protected/runtime/mail
mkdir -p ${APP_SOURCE}/assets && chmod 777 ${APP_SOURCE}/assets


# Generate google api client credentials

if [ "$GIGADB_ENV" == "dev" ] && [ "$REPO_NAME" != "<Your fork name here>" ] ;then
	echo "Retrieving private_key variable for Google API from ${PROJECT_VARIABLES_URL}"
	curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN"  "${PROJECT_VARIABLES_URL}/ANALYTICS_PRIVATE_KEY" | jq -r ' .value' > protected/config/keyfile.json
else
    echo "Either not a dev environment or REPO_NAME set to <Your fork name here>"
    echo "Will try ANALYTICS_PRIVATE_KEY"
    if ! [[ -z ${ANALYTICS_PRIVATE_KEY+x} ]] ;then
    	echo $ANALYTICS_PRIVATE_KEY > protected/config/keyfile.json
    else
        echo "either set REPO_NAME correctly or supply a value for ANALYTICS_PRIVATE_KEY"
    fi
fi

echo "* ---------------------------------------------- *"


# Configure composer.json with dependency versions

SOURCE=${APP_SOURCE}/ops/configuration/php-conf/composer.json.dist
TARGET=${APP_SOURCE}/composer.json
VARS='$COMPOSER_WARNING:$YII_VERSION:$YII2_VERSION:$PHP_VERSION'
envsubst $VARS < $SOURCE > $TARGET

# Generate config files for gigadb-website application using sed

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/console.php.dist
TARGET=${APP_SOURCE}/protected/config/console.php
VARS='$NONE'
envsubst $VARS < $SOURCE > $TARGET


if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
  export YII_TRACE_LEVEL=${YII_TRACE_LEVEL:-"0"}
  export YII_DEBUG=${YII_DEBUG:-"false"}
  export DISABLE_CACHE=${DISABLE_CACHE:-"false"}
else
  export YII_TRACE_LEVEL=${YII_TRACE_LEVEL:-"3"}
  export YII_DEBUG=${YII_DEBUG:-"true"}
  export DISABLE_CACHE=${DISABLE_CACHE:-"true"}
fi;
SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/index.${GIGADB_ENV}.php.dist
TARGET=${APP_SOURCE}/index.php
VARS='$YII_DEBUG:$YII_TRACE_LEVEL:$DISABLE_CACHE'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/yiic.php.dist
TARGET=${APP_SOURCE}/protected/yiic.php
VARS='$YII_PATH:$PHP_MEM'
envsubst $VARS < $SOURCE > $TARGET

# environment specific configuration files

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/help.html.dist
TARGET=${APP_SOURCE}/files/html/help.html
VARS='$HOME_URL'
envsubst $VARS < $SOURCE > $TARGET

export SEARCH_RESULT_LIMIT=${SEARCH_RESULT_LIMIT:-"10"}
SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/local.php.dist
TARGET=${APP_SOURCE}/protected/config/local.php
VARS='$MAILCHIMP_API_KEY:$MAILCHIMP_LIST_ID:$ANALYTICS_CLIENT_EMAIL:$ANALYTICS_CLIENT_ID:$ANALYTICS_KEYFILE_PATH:$HOME_URL:$SERVER_EMAIL:$SERVER_EMAIL_PASSWORD:$SERVER_EMAIL_SMTP_HOST:$RECAPTCHA_PUBLICKEY:$RECAPTCHA_PRIVATEKEY:$GOOGLE_ANALYTICS_PROFILE:$MDS_USERNAME:$MDS_PASSWORD:$MDS_PREFIX:$SEARCH_RESULT_LIMIT:$HASH_SECRET_KEY:$DELETED_DATASETS_USER_EMAIL:$DELETED_DATASETS_USER_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/main.php.dist
TARGET=${APP_SOURCE}/protected/config/main.php
VARS='$OPAUTH_SECURITY_SALT:$FACEBOOK_APP_ID:$FACEBOOK_APP_SECRET:$LINKEDIN_API_KEY:$LINKEDIN_SECRET_KEY:$GOOGLE_CLIENT_ID:$GOOGLE_SECRET:$TWITTER_KEY:$TWITTER_SECRET:$ORCID_CLIENT_ID:$ORCID_CLIENT_SECRET:$ORCID_CLIENT_ENVIRONMENT:$FTP_CONNECTION_URL'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/db.json.dist
TARGET=${APP_SOURCE}/protected/config/db.json
VARS='$GIGADB_DB:$GIGADB_HOST:$GIGADB_USER:$GIGADB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

# Email configuration in web.php differs in dev, CI compared to staging, live 
if [ $GIGADB_ENV = "dev" ] || [ $GIGADB_ENV = "CI" ];
then
  SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/web.dev.CI.php.dist
  TARGET=${APP_SOURCE}/protected/config/yii2/web.php
  VARS='$SERVER_EMAIL_SMTP_HOST:$SERVER_EMAIL_SMTP_PORT:$SERVER_EMAIL:$SERVER_EMAIL_PASSWORD:$AWS_ACCESS_KEY_ID:$AWS_SECRET_ACCESS_KEY'
  envsubst $VARS < $SOURCE > $TARGET
else
  SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/web.staging.live.php.dist
  TARGET=${APP_SOURCE}/protected/config/yii2/web.php
  VARS='$SERVER_EMAIL_SMTP_HOST:$SERVER_EMAIL_SMTP_PORT:$SERVER_EMAIL:$SERVER_EMAIL_PASSWORD:$AWS_ACCESS_KEY_ID:$AWS_SECRET_ACCESS_KEY'
  envsubst $VARS < $SOURCE > $TARGET
fi

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/test.php.dist
TARGET=${APP_SOURCE}/protected/config/yii2/test.php
VARS='$SERVER_EMAIL_SMTP_HOST:$SERVER_EMAIL_SMTP_PORT:$SERVER_EMAIL:$SERVER_EMAIL_PASSWORD:$AWS_ACCESS_KEY_ID:$AWS_SECRET_ACCESS_KEY'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/common/params-local.php.dist
TARGET=${APP_SOURCE}/protected/config/yii2/params-local.php
VARS='$FUW_JWT_KEY:$REMOTE_DOCKER_HOSTNAME:$SERVER_HOSTNAME:$HOME_URL:$FILES_PUBLIC_URL:$GIGADB_ENV'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii2-conf/gigadb/file-worker/db.php.dist
TARGET=${APP_SOURCE}/gigadb/app/worker/file-worker/config/db.php
VARS='$GIGADB_DB:$GIGADB_HOST:$GIGADB_USER:$GIGADB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

if [ $GIGADB_ENV != "CI" ];then
    cp ops/configuration/nginx-conf/le.${GIGADB_ENV}.ini /etc/letsencrypt/cli.ini
fi

## Configuring Nginx config for production environments
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
  SOURCE=${APP_SOURCE}/ops/configuration/nginx-conf/sites/nginx.target_deployment.http.conf.dist
  TARGET=${APP_SOURCE}/ops/configuration/nginx-conf/sites/${GIGADB_ENV}/gigadb.${GIGADB_ENV}.http.conf
  VARS='$SERVER_HOSTNAME'
  envsubst $VARS < $SOURCE > $TARGET

  SOURCE=${APP_SOURCE}/ops/configuration/nginx-conf/sites/nginx.target_deployment.https.conf.dist
  TARGET=${APP_SOURCE}/ops/configuration/nginx-conf/sites/${GIGADB_ENV}/gigadb.${GIGADB_ENV}.https.conf
  VARS='$SERVER_HOSTNAME'
  envsubst $VARS < $SOURCE > $TARGET
fi

## Configuring other tools and apps

SOURCE=${APP_SOURCE}/gigadb/app/tools/files-url-updater/config/params.php.dist
TARGET=${APP_SOURCE}/gigadb/app/tools/files-url-updater/config/params.php
VARS='$DB_BACKUP_HOST:$DB_BACKUP_USERNAME:$DB_BACKUP_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

# Download example dataset files
# mkdir -p ${APP_SOURCE}/vsftpd/files
# if ! [ -f ${APP_SOURCE}/vsftpd/files/ftpexamples4.tar.gz ]; then
#   curl -o ${APP_SOURCE}/vsftpd/files/ftpexamples4.tar.gz https://s3-ap-southeast-1.amazonaws.com/gigadb-ftp-sample-data/ftpexamples4.tar.gz
# fi
# files_count=$(ls -1 ${APP_SOURCE}/vsftpd/files | wc -l)
# if ! [ $files_count -eq 11 ]; then
#   tar -xzvf ${APP_SOURCE}/vsftpd/files/ftpexamples4.tar.gz -C ${APP_SOURCE}/vsftpd/files
# fi

echo "done."
exit 0
