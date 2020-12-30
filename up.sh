#!/usr/bin/env bash

set -e

echo "Starting all services..."

# Make the Docker API available on TCP port 2375 on mac (unnecessary on windows or linux)
docker run --name socat -d -v /var/run/docker.sock:/var/run/docker.sock -p 127.0.0.1:2375:2375 bobrik/socat TCP-LISTEN:2375,fork UNIX-CONNECT:/var/run/docker.sock || true

# Configure the container services
docker-compose run --rm config
docker-compose run --rm fuw-config

# Launch the services required by GigaDB and FUW, and then start nginx (web server) 
docker-compose up gigadb fuw && docker-compose up -d web 

# Compile the CSS files
docker-compose run --rm less

# Install the NPM dependencies for the Javascript application
docker-compose run --rm js bash -c "npm install"

# Start Chome web driver container services for acceptance testing
docker-compose up -d chrome

# Install dependencies for the Beanstalkd workers
docker-compose exec console bash -c 'cd /gigadb-apps/worker/file-worker/ && composer update'

# Start Beanstalkd workers
docker-compose up -d fuw-worker gigadb-worker




