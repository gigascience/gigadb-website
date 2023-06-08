#!/usr/bin/env bash

# bails on error
set -e

# print command being run
set -x


# Check there is .env and source it
if ! [ -f  ./.env ];then
  read -sp "To create .env, enter your private gitlab token: " token
  read -p "To create .env, enter the name of your fork on GitLab: " reponame
  cp config-sources/env.example .env
  sed -i'.bak' "s/#GITLAB_PRIVATE_TOKEN=/GITLAB_PRIVATE_TOKEN=$token/" .env
  sed -i'.bak' "s/REPO_NAME=\"<Your fork name here>\"/REPO_NAME=\"$reponame\"/" .env
  rm .env.bak
fi

source "./.env"


# Check if there is a .secrets file, if not, touch a zero sized one (needed for docker-compose to not fail)
if ! [ -f  ./.secrets ];then
  touch .secrets
fi

# ensure docker-compose is found on bastion
PATH=/usr/local/bin:$PATH
export PATH

# Generate config files from template
docker-compose run --rm config

# Deploy correct Yii2 configuration files
echo "All" | docker-compose run --rm -T console ./init --env=$REVIEW_ENV


# Building services
docker-compose build public api reviewdb console beanstalkd

# running composer update and update
docker-compose run --rm console composer install
docker-compose run --rm console composer update

# Starting the infrastructure services: database, beanstalkd, sftp (not needed on cloud deployment, just for dev and CI)
docker-compose up -d reviewdb
sleep 15
docker-compose up -d beanstalkd sftp_test

# Starting webdriver service for the headless browser used in acceptance testing
if [[ $(uname -m) == 'arm64' ]]; then
  docker-compose up -d chrome-arm
else
  docker-compose up -d chrome
fi

# Make sure DB server is in good state
docker-compose ps
docker-compose logs reviewdb
# (Re)Creating Postgresql database and user for our application
docker-compose run --rm console ./database.sh

# Running database migrations
docker-compose run --rm console ./yii migrate --interactive=0
if [ -f ./yii_test ];then
  docker-compose run --rm console ./yii_test migrate --interactive=0
fi

# Launching all the remaining services
docker-compose up -d public api

# Launch workers
docker-compose up -d manuscripts-worker
