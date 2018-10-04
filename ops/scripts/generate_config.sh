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

# setting up the in-container path to Yii 1.1 framework
YII_PATH="/opt/yii-1.1"


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
    echo "Retrieving variables from ${PROJECT_VARIABLES_URL}"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${PROJECT_VARIABLES_URL}?per_page=100" | jq -r '.[] | select(.key != "ANALYTICS_PRIVATE_KEY") | .key + "=" + .value' > .project_var
    cat .group_var .project_var > .secrets && rm .group_var && rm .project_var
fi
echo "Sourcing secrets"
source "./.secrets"

# restore default settings for variables
set +a


# do the stuff that vagrant would normally do. Even if vagrant is used, doing this stuff regardless is still ok.
mkdir -p ${APP_SOURCE}/protected/runtime && chmod 777 ${APP_SOURCE}/protected/runtime
mkdir -p ${APP_SOURCE}/assets && chmod 777 ${APP_SOURCE}/assets
mkdir -p ${APP_SOURCE}/images/tempcaptcha && chmod 777 ${APP_SOURCE}/images/tempcaptcha


# Generate google api client credentials

if [[ "$GIGADB_ENV" == "dev" ]];then
	echo "Retrieving private_key variable for Google API from ${PROJECT_VARIABLES_URL}"
	curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN"  "${PROJECT_VARIABLES_URL}/ANALYTICS_PRIVATE_KEY" | jq -r ' .value' > protected/config/keyfile.json
else
	echo $ANALYTICS_PRIVATE_KEY > protected/config/keyfile.json
fi

echo "* ---------------------------------------------- *"

# Generate nginx site config

SOURCE=${APP_SOURCE}/ops/configuration/nginx-conf/sites/gigadb.conf.dist
TARGET=/etc/nginx/sites-available/gigadb.conf
VARS='$HOME_URL'
envsubst $VARS < $SOURCE > $TARGET

# Configure composer.json with dependency versions

SOURCE=${APP_SOURCE}/ops/configuration/php-conf/composer.json.dist
TARGET=${APP_SOURCE}/composer.json
VARS='$YII_VERSION:$PHP_VERSION'
envsubst $VARS < $SOURCE > $TARGET

# Generate config files for gigadb-website application using sed


SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/console.php.dist
TARGET=${APP_SOURCE}/protected/config/console.php
VARS='$NONE'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/index.${GIGADB_ENV}.php.dist
TARGET=${APP_SOURCE}/index.php
VARS='$YII_PATH:$APP_SOURCE'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/yii.php.dist
TARGET=${APP_SOURCE}/protected/yiic.php
VARS='$YII_PATH'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/local.php.dist
TARGET=${APP_SOURCE}/protected/config/local.php
VARS='$MAILCHIMP_API_KEY:$MAILCHIMP_LIST_ID:$ANALYTICS_CLIENT_EMAIL:$ANALYTICS_CLIENT_ID:$ANALYTICS_KEYFILE_PATH:$HOME_URL:$SERVER_EMAIL:$RECAPTCHA_PUBLICKEY:$RECAPTCHA_PRIVATEKEY:$GOOGLE_ANALYTICS_PROFILE:$MDS_USERNAME:$MDS_PASSWORD:$MDS_PREFIX'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/main.php.dist
TARGET=${APP_SOURCE}/protected/config/main.php
VARS='$FACEBOOK_APP_ID:$FACEBOOK_APP_SECRET:$LINKEDIN_API_KEY:$LINKEDIN_SECRET_KEY:$GOOGLE_CLIENT_ID:$GOOGLE_SECRET:$TWITTER_KEY:$TWITTER_SECRET:$ORCID_CLIENT_ID:$ORCID_CLIENT_SECRET:$ORCID_CLIENT_ENVIRONMENT:$FTP_CONNECTION_URL'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/db.json.dist
TARGET=${APP_SOURCE}/protected/config/db.json
VARS='$GIGADB_DB:$GIGADB_HOST:$GIGADB_USER:$GIGADB_PASSWORD'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/es.json.dist
TARGET=${APP_SOURCE}/protected/config/es.json
VARS='$GIGADB_ES_PORT'
envsubst $VARS < $SOURCE > $TARGET

SOURCE=${APP_SOURCE}/ops/configuration/yii-conf/help.html.dist
TARGET=${APP_SOURCE}/files/html/help.html
VARS='$HOME_URL'
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
