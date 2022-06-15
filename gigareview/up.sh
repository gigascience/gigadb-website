#!/usr/bin/env bash

# bails on error
set -e

# print command being run
set -x


# Check there is .env
if ! [ -f  ./.env ];then
  read -sp "To create .env, enter your private gitlab token: " token
  read -p "To create .env, enter the name of your fork on GitLab: " reponame
  cp config-sources/env.example .env
  sed -i'.bak' "s/#GITLAB_PRIVATE_TOKEN=/GITLAB_PRIVATE_TOKEN=$token/" .env
  sed -i'.bak' "s/REPO_NAME=\"<Your fork name here>\"/REPO_NAME=\"$reponame\"/" .env
  rm .env.bak
fi

# Generate config files from template
docker-compose run --rm config

# Deploy correct Yii2 configuration files
echo "All" | docker-compose run --rm console ./init --env=Development


# Building services
docker-compose build public api reviewdb console beanstalkd

# running composer update
docker-compose run --rm console composer update

# Starting the infrastructure services: database, beanstalkd, sftp (not needed on cloud deployment, just for dev and CI)
docker-compose up -d reviewdb
sleep 15
docker-compose up -d beanstalkd sftp_test

# Starting webdriver service for the headless browser used in acceptance testing
docker-compose up -d webdriver

# (Re)Creating Postgresql database and user for our application
docker-compose run --rm console ./database.sh

# Running database migrations
docker-compose run --rm console ./yii migrate --interactive=0
docker-compose run --rm console ./yii_test migrate --interactive=0


# Launching all the remaining services
docker-compose up -d public api

