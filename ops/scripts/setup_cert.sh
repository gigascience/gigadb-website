#!/usr/bin/env bash

# bail out upon error
set -e

# bail out if an unset variable is used
set -u


# Load .env se we can access CSV_DIR
source "./.env"


# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.staging.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

echo "Checking whether the certificate exists"
docker-compose exec web ls -alrt /etc/letsencrypt/live/$REMOTE_HOSTNAME/fullchain.pem

if [[ $? -eq 0 ]];then
	echo "Renewing the certificate for $REMOTE_HOSTNAME"
	$DOCKER_COMPOSE run --rm certbot renew
else
	echo "Creating the certificate for $REMOTE_HOSTNAME"
	$DOCKER_COMPOSE run --rm certbot certonly -d $REMOTE_HOSTNAME
fi