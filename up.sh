#!/usr/bin/env bash

# bails on error
set -e

# print command being run
set -x

dbSet=${1:-"dev"}


echo "Starting all services..."

# Make the Docker API available on TCP port 2375 on mac (unnecessary on windows or linux)
if [ "$(uname)" == "Darwin" ];then
  docker stop socat && docker rm socat
  docker run --name socat -d -v /var/run/docker.sock:/var/run/docker.sock -p 127.0.0.1:2375:2375 bobrik/socat TCP-LISTEN:2375,fork UNIX-CONNECT:/var/run/docker.sock || true
fi;

# Check there is .env
if ! [ -f  ./.env ];then
  read -sp "To create .env, enter your private gitlab token and name of the name of your fork on GitLab: " token
  read -p "To create .env, enter the name of your fork on GitLab: " reponame
  cp ops/configuration/variables/env-sample .env
  sed -i'.bak' "s/#GITLAB_PRIVATE_TOKEN=/GITLAB_PRIVATE_TOKEN=$token/" .env
  sed -i'.bak' "s/REPO_NAME=\"<Your fork name here>\"/REPO_NAME=\"$reponame\"/" .env
  rm .env.bak
fi

# write down application version
git describe --always > VERSION

# Configure the container services
docker-compose run --rm config
docker-compose run --rm fuw-config

# start the container admin UI (not in CI)
if [ "$(uname)" == "Darwin" ];then
  ./ops/scripts/start_portainer.sh
fi;

# Build console and web containers (needed when switching between branches often)
docker-compose build web test

# Launch the services required by GigaDB and FUW, and then start nginx (web server)
docker-compose up -d --build application database fuw-public fuw-admin console

# Execute the following to start tideways service for profiling the local gigadb website
# docker-compose up -d --build tideways-daemon

# start web server
docker-compose up -d web

# Install composer dependencies for GigaDB
docker-compose exec -T application composer install

# Compile the CSS files
docker-compose run --rm less

# Install composer dependencies for FUW
docker-compose exec -T fuw-admin composer install

# Install the NPM dependencies for the Javascript application and the ops scripts
docker-compose run --rm js bash -c "npm install"
docker-compose run --rm js bash -c "cd /var/www/ops/scripts/ && npm install"

# Build and deploy the Javascript application
docker-compose run --rm js

# Start Chome web driver container services for acceptance testing
docker-compose up -d chrome

# Install dependencies for the Beanstalkd workers
docker-compose exec -T console bash -c 'cd /gigadb-apps/worker/file-worker/ && composer install'

# Start Beanstalkd workers after running the required migrations
docker-compose exec -T console /app/yii migrate/fresh --interactive=0
docker-compose up -d fuw-worker gigadb-worker

# Start

# Bootstrap the main database using data from "data/dev" by default or using the one passed as parameter
./ops/scripts/setup_devdb.sh $dbSet

# Bootstrap the test database for unit tests using data from "data/gigadb_testdata"
./ops/scripts/setup_testdb.sh gigadb_testdata

# create the database dumps needed by functional tests and acceptance tests
./ops/scripts/make_pgdmp_gigadb_testdata.sh
./ops/scripts/make_pgdmp_production_like.sh
./ops/scripts/make_pgdmp_gigadb.sh

# generate reference data feed for file formats and file types
docker-compose run --rm test ./protected/yiic generatefiletypes
docker-compose run --rm test ./protected/yiic generatefileformats

#show status of all containers
docker ps
