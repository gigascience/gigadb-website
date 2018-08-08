#!/usr/bin/env bash

# bail out upon error
# set -e
# display the lines of this script as they are executed
# set -x

# Print directory of this script. We will need it to find nginx config

THIS_SCRIPT_DIR=`dirname "$BASH_SOURCE"`
echo "Running ${THIS_SCRIPT_DIR}/generate_config.sh"

echo "* ---------------------------------------------- *"


# read env variables in same directory, from a file called .env.
# They are shared by both this script and Docker compose files.

echo "Current working directory: $PWD"
if ! [ -f  ./.env ];then
    echo "ERROR: There is no .env file in this directory. Cannot run the configuration."
    echo "Please, switch to a directory with an .env file before running the configuration"
    exit 1
fi
source "./.env"

# setting up the default for the new variables (introduced for the audit report) so old .env still work

COMPOSE_PROJECT_NAME=${COMPOSE_PROJECT_NAME:-gigadb}
YII_VERSION=${YII_VERSION:-1.1.16}

# for diagnostics purpose, print the value for the paths related variables need for successful configuration
echo "HOME_URL: ${HOME_URL}"
echo "NGINX_HOST_HTTP_PORT: ${NGINX_HOST_HTTP_PORT}"
echo "NGINX_HOST_HTTPS_PORT: ${NGINX_HOST_HTTPS_PORT}"
echo "POSTGRES_PORT: ${POSTGRES_PORT}"
echo "WORKSPACE_SSH_PORT: ${WORKSPACE_SSH_PORT}"

echo "Yii version: ${YII_VERSION}"
echo "Yii path: ${YII_PATH}"
echo "Application path: ${APPLICATION}"
echo "COMPOSE_PROJECT_NAME: ${COMPOSE_PROJECT_NAME}"
echo "COMPOSE_FILE: ${COMPOSE_FILE}"

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
    && sed "/<% aws = node\[:aws\] -%>/d" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= aws\[:aws_access_key_id\] %>|${AWS_ACCESS_KEY_ID}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= aws\[:aws_secret_access_key\] %>|${AWS_SECRET_ACCESS_KEY}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= aws\[:s3_bucket_for_file_bundles\] %>|${AWS_S3_BUCKET_FOR_FILE_BUNDLES}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= aws\[:s3_bucket_for_file_previews\] %>|${AWS_S3_BUCKET_FOR_FILE_PREVIEWS}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= aws\[:aws_default_region\] %>|${AWS_DEFAULT_REGION}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-console.php.erb
TARGET=${APPLICATION}/protected/config/console.php
cp $SOURCE $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:mfr\]\[:preview_server\] %>|${PREVIEW_SERVER_HOST}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:ftp\]\[:connection_url\] %>|${FTP_CONNECTION_URL}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:multidownload\]\[:download_host\] %>|${MULTIDOWNLOAD_SERVER_HOST}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:redis\]\[:server\] %>|${REDIS_SERVER_HOST}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:beanstalk\]\[:host\] %>|${BEANSTALK_SERVER_HOST}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-index.php.erb
TARGET=${APPLICATION}/index.php
cp $SOURCE $TARGET \
    && sed "/<% path = node\[:yii\]\[:path\] -%>/d" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= path %>|${YII_PATH}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yiic.php.erb
TARGET=${APPLICATION}/protected/yiic.php
cp $SOURCE $TARGET \
    && sed "/<% path = node\[:yii\]\[:path\] -%>/d" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= path %>|${YII_PATH}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-local.php.erb
TARGET=${APPLICATION}/protected/config/local.php
cp $SOURCE $TARGET \
    && sed "/<% home_url = node\[:gigadb\]\[:server_names\] -%>/d" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "/<% server_email = node\[:gigadb\]\[:admin_email\] -%>/d" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:mailchimp\]\[:mailchimp_api_key\] %>|${MAILCHIMP_API_KEY}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:mailchimp\]\[:mailchimp_list_id\] %>|${MAILCHIMP_LIST_ID}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:analytics\]\[:analytics_client_email\] %>|${ANALYTICS_CLIENT_EMAIL}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:analytics\]\[:analytics_client_id\] %>|${ANALYTICS_CLIENT_ID}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:analytics\]\[:analytics_keyfile_path\] %>|${ANALYTICS_KEYFILE_PATH}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= home_url %>|${HOME_URL}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= home_url %>|${SERVER_EMAIL}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:recaptcha\]\[:recaptcha_publickey\] %>|${RECAPTCHA_PUBLICKEY}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:recaptcha\]\[:recaptcha_privatekey\] %>|${RECAPTCHA_PRIVATEKEY}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:analytics\]\[:google_analytics_profile\] %>|${GOOGLE_ANALYTICS_PROFILE}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:mds\]\[:mds_username\] %>|${MDS_USERNAME}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:mds\]\[:mds_password\] %>|${MDS_PASSWORD}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:mds\]\[:mds_prefix\] %>|${MDS_PREFIX}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-main.php.erb
TARGET=${APPLICATION}/protected/config/main.php
cp $SOURCE $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:facebook\]\[:app_id\] %>|${FACEBOOK_APP_ID}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:facebook\]\[:app_secret\] %>|${FACEBOOK_APP_SECRET}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:linkedin\]\[:api_key\] %>|${LINKEDIN_API_KEY}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:linkedin\]\[:secret_key\] %>|${LINKEDIN_SECRET_KEY}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:google\]\[:client_id\] %>|${GOOGLE_CLIENT_ID}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:google\]\[:client_secret\] %>|${GOOGLE_SECRET}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:twitter\]\[:key\] %>|${TWITTER_KEY}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:twitter\]\[:secret\] %>|${TWITTER_SECRET}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:orcid\]\[:client_id\] %>|${ORCID_CLIENT_ID}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:orcid\]\[:client_secret\] %>|${ORCID_CLIENT_SECRET}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:ftp\]\[:connection_url\] %>|${FTP_CONNECTION_URL}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:redis\]\[:server\] %>|${REDIS_SERVER_HOST}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:beanstalk\]\[:host\] %>|${BEANSTALK_SERVER_HOST}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:mfr\]\[:preview_server\] %>|${PREVIEW_SERVER_HOST}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-db.json.erb
TARGET=${APPLICATION}/protected/config/db.json
cp $SOURCE $TARGET \
    && sed "/<% db = node\[:gigadb\]\[:db\] -%>/d" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= db\[:database\] %>|gigadb|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= db\[:host\] %>|${GIGADB_HOST}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= db\[:user\] %>|${GIGADB_USER}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= db\[:password\] %>|${GIGADB_PASSWORD}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/set_env.sh.erb
TARGET=${APPLICATION}/protected/scripts/set_env.sh
cp $SOURCE $TARGET \
    && sed "/<% db = node\[:gigadb\]\[:db\] -%>/d" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= db\[:database\] %>|${GIGADB_DATABASE}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= db\[:host\] %>|${GIGADB_HOST}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= db\[:user\] %>|${GIGADB_USER}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= db\[:password\] %>|${GIGADB_PASSWORD}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/es.json.erb
TARGET=${APPLICATION}/protected/config/es.json
cp $SOURCE $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:es_port\] %>|${GIGADB_ES_PORT}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/update_links.sh.erb
TARGET=${APPLICATION}/protected/scripts/update_links.sh
cp $SOURCE $TARGET \
    && sed "s|<%= node\[:gigadb\]\[:db\]\[:password\] %>|${GIGADB_PASSWORD}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

SOURCE=${APPLICATION}/chef/site-cookbooks/gigadb/templates/default/yii-help.html.erb
TARGET=${APPLICATION}/files/html/help.html
cp $SOURCE $TARGET \
    && sed "/<% path = node\[:yii\]\[:ip_address\] -%>/d" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && sed "s|<%= path %>|${HOME_URL}|g" $TARGET > $TARGET.new && mv $TARGET $TARGET.bak && mv $TARGET.new $TARGET \
    && rm $TARGET.bak

# Download Yii version $YII_VERSION if not yet downloaded
YII_URL=$(curl -s https://github.com/yiisoft/yii/releases/tag/${YII_VERSION} | grep "yii-${YII_VERSION}" | grep "tar.gz" | sed -n 's/.*href="\([^"]*\).*/\1/p')
if ! [ -f  yiirelease-${YII_VERSION}.tar.gz ];then
    echo "Downloading the Yii framework ${YII_VERSION}"
	curl -o yiirelease-${YII_VERSION}.tar.gz -L "https://github.com${YII_URL}"
fi

# Install Yii of version $YII_VERSION in the ~/.laradock/data directory for persistent container data, if not yet installed
YII_FRAMEWORK="${DATA_SAVE_PATH}/${COMPOSE_PROJECT_NAME}/yii"
if ! [ -f "$YII_FRAMEWORK/version-${YII_VERSION}" ]; then
    echo "Installing the Yii framework ${YII_VERSION} to $YII_FRAMEWORK"
    mkdir -p $YII_FRAMEWORK
    rm "$YII_FRAMEWORK/version-${YII_VERSION}"
    tar xvzf yiirelease-${YII_VERSION}.tar.gz && mv $YII_FRAMEWORK ${YII_FRAMEWORK}.bak
    mv yii-1.1.* $YII_FRAMEWORK && rm -rf ${YII_FRAMEWORK}.bak
    touch $YII_FRAMEWORK/version-${YII_VERSION}
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


echo "* ---------------------------------------------- *"
echo "done."
echo "* ---------------------------------------------- *"
echo "To instantiate your website, you can now type the command below and it will be launched at http://${HOME_URL}:${NGINX_HOST_HTTP_PORT}"
echo "docker-compose up -d init"
echo "* ---------------------------------------------- *"
exit 0
