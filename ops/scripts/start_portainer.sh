#!/usr/bin/env bash


# bail out as soon as there is an error
set -eux

# Load environment variables
source "./.env"
source "./.secrets"

# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.production-envs.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

if ! [ -z "${PORTAINER_BCRYPT+x}" ];then
  echo "PORTAINER_BCRYPT value has been set in .env already"
  # start portainer in detached mode and make sure volume are recreated (rather than use potential previous state that my be erroneous)
  $DOCKER_COMPOSE up --detach --renew-anon-volumes portainer
else
  echo "PORTAINER_BCRYPT value is empty"
  echo "Generate bcrypt from password"
  P_BCRYPT=$(docker run --rm httpd:2.4-alpine htpasswd -nbB admin $PORTAINER_PASSWORD | cut -d ":" -f 2 | sed -e 's/\$/\\\$/g')
  echo "PORTAINER_BCRYPT=$P_BCRYPT" >> .env
  # start portainer in detached mode and make sure volume are recreated (rather than use potential previous state that my be erroneous)
  $DOCKER_COMPOSE up --detach --renew-anon-volumes portainer
fi