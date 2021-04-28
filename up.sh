#!/usr/bin/env bash

# bails on error
set -e

# print command being run
set -x

echo "Starting all services..."

# Make the Docker API available on TCP port 2375 on mac (unnecessary on windows or linux)
docker stop socat && docker rm socat
docker run --name socat -d -v /var/run/docker.sock:/var/run/docker.sock -p 127.0.0.1:2375:2375 bobrik/socat TCP-LISTEN:2375,fork UNIX-CONNECT:/var/run/docker.sock || true

# Configure the container services
docker-compose run --rm config
docker-compose run --rm fuw-config

# Build console and web containers (needed when switching between branches often)
docker-compose build web test console

# Launch the services required by GigaDB and FUW, and then start nginx (web server) 
docker-compose up -d --build application database fuw-public fuw-admin console

# start web server
docker-compose up -d web

# Install composer dependencies for GigaDB
docker-compose run gigadb

# Compile the CSS files
docker-compose run --rm less

# Install composer dependencies for FUW
docker-compose run fuw

# Install the NPM dependencies for the Javascript application and the ops scripts
docker-compose run --rm js bash -c "npm install"
docker-compose run --rm js bash -c "cd /var/www/ops/scripts/ && npm install"

# Build and deploy the Javascript application
docker-compose run --rm js

# Start Chome web driver container services for acceptance testing
docker-compose up -d chrome

# Install dependencies for the Beanstalkd workers
docker-compose exec console bash -c 'cd /gigadb-apps/worker/file-worker/ && composer update'

# Start Beanstalkd workers after running the required migrations
docker-compose exec console /app/yii migrate/fresh --interactive=0
docker-compose up -d fuw-worker gigadb-worker

# Bootstrap the main database using data from "data/dev"
./ops/scripts/setup_devdb.sh dev

# Bootstrap the test database for unit tests using data from "data/gigadb_testdata"
./ops/scripts/setup_testdb.sh gigadb_testdata

# create the database dumps needed by functional tests and acceptance tests
./ops/scripts/make_pgdmp_gigadb_testdata.sh
./ops/scripts/make_pgdmp_production_like.sh



