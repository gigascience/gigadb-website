#!/usr/bin/env bash

# bails on error
set -e

# print command being run
set -x


# Check there is .env
if ! [ -f  ./.env ];then
  read -sp "To create .env, enter your private gitlab token and name of the name of your fork on GitLab: " token
  read -p "To create .env, enter the name of your fork on GitLab: " reponame
  cp env-sample .env
  sed -i'.bak' "s/#GITLAB_PRIVATE_TOKEN=/GITLAB_PRIVATE_TOKEN=$token/" .env
  sed -i'.bak' "s/REPO_NAME=\"<Your fork name here>\"/REPO_NAME=\"$reponame\"/" .env
  rm .env.bak
fi

# Generate config files from template
docker-compose run --rm config

# Deploy correct Yii2 configuration files
echo "All" | docker-compose run --rm console ./init --env=Development


# Building services
docker-compose build public api reviewdb console

# running composer update
docker-compose run --rm console composer update

# Launching service
docker-compose up -d public api reviewdb