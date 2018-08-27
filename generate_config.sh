#!/usr/bin/env bash

# bail out upon error
set -e

# bail out if an unset variable is used
set -u

# display the lines of this script as they are executed for debugging
# set -x

# read env variables in same directory, from a file called .env.
# They are shared by both this script and Docker compose files.
cd /var/www
echo "Current working directory: $PWD"
if ! [ -f  ./.env ];then
    echo "ERROR: There is no .env file in this directory. Cannot run the configuration."
    echo "Please, switch to a directory with an .env file before running the configuration"
    exit 1
fi
source "./.env"

# Print directory of this script. We will need it to find nginx config
THIS_SCRIPT_DIR=`dirname "$BASH_SOURCE"`
echo "Running ${THIS_SCRIPT_DIR}/generate_config.sh for environment: $GIGADB_ENV"

echo "* ---------------------------------------------- *"


# for diagnostics purpose, print the value of .env variables
echo "COMPOSE_PROJECT_NAME: $COMPOSE_PROJECT_NAME"
echo "Application path: $APPLICATION"
echo "Data path: $DATA_SAVE_PATH"

echo "HOME_URL: $HOME_URL"
echo "SERVER_EMAIL: $SERVER_EMAIL"
echo "PUBLIC_HTTP_PORT: $PUBLIC_HTTP_PORT"
echo "PUBLIC_HTTPS_PORT: $PUBLIC_HTTPS_PORT"


# fetch and set environment variables from GitLab
# Only necessary on DEV, as on CI (STG and PROD), the variables are exposed to the deploy process

if [[ "$GIGADB_ENV" == "DEV" ]];then
    echo "Retrieving variables from GitLab"
    curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "${JSON_VARIABLES_URL}" | jq -r '.[] | select(.key|startswith("DEV")) |  .key + "=\"" + .value + "\""' > .secrets

    echo "Sourcing secrets"
    source "./.secrets"
fi

echo "* ---------------------------------------------- *"

# do the stuff that vagrant would normally do. Even if vagrant is used, doing this stuff regardless is still ok.
mkdir -p ${APPLICATION}/protected/runtime
mkdir -p ${APPLICATION}/assets
mkdir -p ${APPLICATION}/images/tempcaptcha
chmod 777 ${APPLICATION}/protected/runtime
chmod 777 ${APPLICATION}/assets
chmod 777 ${APPLICATION}/images/tempcaptcha

# Generate nginx site config

mkdir -p ${DATA_SAVE_PATH}/${COMPOSE_PROJECT_NAME}/nginx/sites-available
sed "s|192.168.42.10|${HOME_URL}|" $THIS_SCRIPT_DIR/nginx-conf/sites/gigadb.conf > ${DATA_SAVE_PATH}/${COMPOSE_PROJECT_NAME}/nginx/sites-available/${COMPOSE_PROJECT_NAME}.conf

# Generate config files for gigadb-website application using sed

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-aws.json.erb
TARGET=${APPLICATION}/protected/config/aws.json
cp $SOURCE $TARGET \
    && sed -i \
    -e "/<% aws = node\[:aws\] -%>/d" \
    -e "s|<%= aws\[:aws_access_key_id\] %>|${AWS_ACCESS_KEY_ID}|g" \
    -e "s|<%= aws\[:aws_secret_access_key\] %>|${AWS_SECRET_ACCESS_KEY}|g" \
    -e "s|<%= aws\[:s3_bucket_for_file_bundles\] %>|${AWS_S3_BUCKET_FOR_FILE_BUNDLES}|g" \
    -e "s|<%= aws\[:s3_bucket_for_file_previews\] %>|${AWS_S3_BUCKET_FOR_FILE_PREVIEWS}|g" \
    -e "s|<%= aws\[:aws_default_region\] %>|${AWS_DEFAULT_REGION}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-console.php.erb
TARGET=${APPLICATION}/protected/config/console.php
cp $SOURCE $TARGET \
    && sed -i \
    -e "s|<%= node\[:gigadb\]\[:mfr\]\[:preview_server\] %>|${PREVIEW_SERVER_HOST}|g" \
    -e "s|<%= node\[:gigadb\]\[:ftp\]\[:connection_url\] %>|${FTP_CONNECTION_URL}|g" \
    -e "s|<%= node\[:gigadb\]\[:multidownload\]\[:download_host\] %>|${MULTIDOWNLOAD_SERVER_HOST}|g" \
    -e "s|<%= node\[:gigadb\]\[:redis\]\[:server\] %>|${REDIS_SERVER_HOST}|g" \
    -e "s|<%= node\[:gigadb\]\[:beanstalk\]\[:host\] %>|${BEANSTALK_SERVER_HOST}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-index.php.erb
TARGET=${APPLICATION}/index.php
cp $SOURCE $TARGET \
    && sed -i \
    -e "/<% path = node\[:yii\]\[:path\] -%>/d" \
    -e "s|<%= path %>|${YII_PATH}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yiic.php.erb
TARGET=${APPLICATION}/protected/yiic.php
cp $SOURCE $TARGET \
    && sed -i \
    -e "/<% path = node\[:yii\]\[:path\] -%>/d" \
    -e "s|<%= path %>|${YII_PATH}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-local.php.erb
TARGET=${APPLICATION}/protected/config/local.php
cp $SOURCE $TARGET \
    && sed -i \
    -e "/<% home_url = node\[:gigadb\]\[:server_names\] -%>/d" \
    -e "/<% server_email = node\[:gigadb\]\[:admin_email\] -%>/d" \
    -e "s|<%= node\[:gigadb\]\[:mailchimp\]\[:mailchimp_api_key\] %>|${MAILCHIMP_API_KEY}|g" \
    -e "s|<%= node\[:gigadb\]\[:mailchimp\]\[:mailchimp_list_id\] %>|${MAILCHIMP_LIST_ID}|g" \
    -e "s|<%= node\[:gigadb\]\[:analytics\]\[:analytics_client_email\] %>|${ANALYTICS_CLIENT_EMAIL}|g" \
    -e "s|<%= node\[:gigadb\]\[:analytics\]\[:analytics_client_id\] %>|${ANALYTICS_CLIENT_ID}|g" \
    -e "s|<%= node\[:gigadb\]\[:analytics\]\[:analytics_keyfile_path\] %>|${ANALYTICS_KEYFILE_PATH}|g" \
    -e "s|<%= home_url %>|${HOME_URL}|g" \
    -e "s|<%= home_url %>|${SERVER_EMAIL}|g" \
    -e "s|<%= node\[:gigadb\]\[:recaptcha\]\[:recaptcha_publickey\] %>|${RECAPTCHA_PUBLICKEY}|g" \
    -e "s|<%= node\[:gigadb\]\[:recaptcha\]\[:recaptcha_privatekey\] %>|${RECAPTCHA_PRIVATEKEY}|g" \
    -e "s|<%= node\[:gigadb\]\[:analytics\]\[:google_analytics_profile\] %>|${GOOGLE_ANALYTICS_PROFILE}|g" \
    -e "s|<%= node\[:gigadb\]\[:mds\]\[:mds_username\] %>|${MDS_USERNAME}|g" \
    -e "s|<%= node\[:gigadb\]\[:mds\]\[:mds_password\] %>|${MDS_PASSWORD}|g" \
    -e "s|<%= node\[:gigadb\]\[:mds\]\[:mds_prefix\] %>|${MDS_PREFIX}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-main.php.erb
TARGET=${APPLICATION}/protected/config/main.php
cp $SOURCE $TARGET \
    && sed -i \
    -e "s|<%= node\[:gigadb\]\[:facebook\]\[:app_id\] %>|${FACEBOOK_APP_ID}|g" \
    -e "s|<%= node\[:gigadb\]\[:facebook\]\[:app_secret\] %>|${FACEBOOK_APP_SECRET}|g" \
    -e "s|<%= node\[:gigadb\]\[:linkedin\]\[:api_key\] %>|${LINKEDIN_API_KEY}|g" \
    -e "s|<%= node\[:gigadb\]\[:linkedin\]\[:secret_key\] %>|${LINKEDIN_SECRET_KEY}|g" \
    -e "s|<%= node\[:gigadb\]\[:google\]\[:client_id\] %>|${GOOGLE_CLIENT_ID}|g" \
    -e "s|<%= node\[:gigadb\]\[:google\]\[:client_secret\] %>|${GOOGLE_SECRET}|g" \
    -e "s|<%= node\[:gigadb\]\[:twitter\]\[:key\] %>|${TWITTER_KEY}|g" \
    -e "s|<%= node\[:gigadb\]\[:twitter\]\[:secret\] %>|${TWITTER_SECRET}|g" \
    -e "s|<%= node\[:gigadb\]\[:orcid\]\[:client_id\]  %>|${ORCID_CLIENT_ID}|g" \
    -e "s|<%= node\[:gigadb\]\[:orcid\]\[:client_secret\] %>|${ORCID_CLIENT_SECRET}|g" \
    -e "s|<%= node\[:gigadb\]\[:orcid\]\[:environment\] %>|${ORCID_CLIENT_ENVIRONMENT}|g" \
    -e "s|<%= node\[:gigadb\]\[:ftp\]\[:connection_url\] %>|${FTP_CONNECTION_URL}|g" \
    -e "s|<%= node\[:gigadb\]\[:redis\]\[:server\] %>|${REDIS_SERVER_HOST}|g" \
    -e "s|<%= node\[:gigadb\]\[:beanstalk\]\[:host\] %>|${BEANSTALK_SERVER_HOST}|g" \
    -e "s|<%= node\[:gigadb\]\[:mfr\]\[:preview_server\] %>|${PREVIEW_SERVER_HOST}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-db.json.erb
TARGET=${APPLICATION}/protected/config/db.json
cp $SOURCE $TARGET \
    && sed -i \
    -e "/<% db = node\[:gigadb\]\[:db\] -%>/d" \
    -e "s|<%= db\[:database\] %>|${GIGADB_DB}|g" \
    -e "s|<%= db\[:host\] %>|${GIGADB_HOST}|g" \
    -e "s|<%= db\[:user\] %>|${GIGADB_USER}|g" \
    -e "s|<%= db\[:password\] %>|${GIGADB_PASSWORD}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/set_env.sh.erb
TARGET=${APPLICATION}/protected/scripts/set_env.sh
cp $SOURCE $TARGET \
    && sed -i \
    -e "/<% db = node\[:gigadb\]\[:db\] -%>/d" \
    -e "s|<%= db\[:database\] %>|${GIGADB_DB}|g" \
    -e "s|<%= db\[:host\] %>|${GIGADB_HOST}|g" \
    -e "s|<%= db\[:user\] %>|${GIGADB_USER}|g" \
    -e "s|<%= db\[:password\] %>|${GIGADB_PASSWORD}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/es.json.erb
TARGET=${APPLICATION}/protected/config/es.json
cp $SOURCE $TARGET \
    && sed -i \
    -e "s|<%= node\[:gigadb\]\[:es_port\] %>|${GIGADB_ES_PORT}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/update_links.sh.erb
TARGET=${APPLICATION}/protected/scripts/update_links.sh
cp $SOURCE $TARGET \
    && sed -i \
    -e "s|<%= node\[:gigadb\]\[:db\]\[:password\] %>|${GIGADB_PASSWORD}|g" \
    $TARGET

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-help.html.erb
TARGET=${APPLICATION}/files/html/help.html
cp $SOURCE $TARGET \
    && sed -i \
    -e "/<% path = node\[:yii\]\[:ip_address\] -%>/d" \
    -e "s|<%= path %>|${HOME_URL}|g" \
    $TARGET

# Download Yii version $YII_VERSION if not yet downloaded
YII_URL=$(curl -s https://github.com/yiisoft/yii/releases/tag/${YII_VERSION} | grep "yii-${YII_VERSION}" | grep "tar.gz" | sed -n 's/.*href="\([^"]*\).*/\1/p')
if ! [ -f  yiirelease-${YII_VERSION}.tar.gz ];then
    echo "Downloading the Yii framework ${YII_VERSION}"
	curl -o yiirelease-${YII_VERSION}.tar.gz -L "https://github.com${YII_URL}"
fi

# Install Yii of version $YII_VERSION in the ~/.laradock/data directory for persistent container data, if not yet installed
if ! [ -f "$YII_PATH/version-${YII_VERSION}" ]; then
    echo "Installing the Yii framework ${YII_VERSION} to $YII_PATH"
    mkdir -p $YII_PATH
    rm "$YII_PATH/version-${YII_VERSION}"
    tar xvzf yiirelease-${YII_VERSION}.tar.gz && mv $YII_PATH ${YII_PATH}.bak
    mv yii-1.1.* $YII_PATH && rm -rf ${YII_PATH}.bak
    touch $YII_PATH/version-${YII_VERSION}
fi


# Download example dataset files
mkdir -p ${APPLICATION}/vsftpd/files
if ! [ -f ${APPLICATION}/vsftpd/files/ftpexamples4.tar.gz ]; then
  curl -o ${APPLICATION}/vsftpd/files/ftpexamples4.tar.gz https://s3-ap-southeast-1.amazonaws.com/gigadb-ftp-sample-data/ftpexamples4.tar.gz
fi
files_count=$(ls -1 ${APPLICATION}/vsftpd/files | wc -l)
if ! [ $files_count -eq 11 ]; then
  tar -xzvf ${APPLICATION}/vsftpd/files/ftpexamples4.tar.gz -C ${APPLICATION}/vsftpd/files
fi


echo "done."
exit 0
