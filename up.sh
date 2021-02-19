#!/usr/bin/env bash

set -e

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
docker-compose up --build gigadb fuw && docker-compose up -d web

# Compile the CSS files
docker-compose run --rm less

# Install the NPM dependencies for the Javascript application and the ops scripts
docker-compose run --rm js bash -c "npm install"
docker-compose run --rm js bash -c "cd /var/www/ops/scripts/ && npm install"

# Build and deploy the Javascript application
docker-compose run --rm js

# Start Chome web driver container services for acceptance testing
docker-compose up -d chrome

# Install dependencies for the Beanstalkd workers
docker-compose exec console bash -c 'cd /gigadb-apps/worker/file-worker/ && composer update'

# Start Beanstalkd workers
docker-compose up -d fuw-worker gigadb-worker

# Setup the main and test databases
./ops/scripts/setup_devdb.sh
./ops/scripts/setup_testdb.sh


