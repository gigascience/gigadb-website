#!/usr/bin/env bash

# bail out if an unset variable is used
set -u


# Load .env se we can access CSV_DIR
source "./.env"

# Path to the certs
FULLCHAIN_PEM=/etc/letsencrypt/$GIGADB_ENV/$REMOTE_HOSTNAME/fullchain.pem
PRIVATE_PEM=/etc/letsencrypt/$GIGADB_ENV/$REMOTE_HOSTNAME/privkey.pem
CHAIN_PEM=/etc/letsencrypt/$GIGADB_ENV/$REMOTE_HOSTNAME/chain.pem

# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.production-envs.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

echo "Checking whether the certificate exists"
$DOCKER_COMPOSE exec -T web test -f /etc/letsencrypt/$GIGADB_ENV/$REMOTE_HOSTNAME/fullchain.pem && \
$DOCKER_COMPOSE exec -T web test -f /etc/letsencrypt/$GIGADB_ENV/$REMOTE_HOSTNAME/privkey.pem && \
$DOCKER_COMPOSE exec -T web test -f /etc/letsencrypt/$GIGADB_ENV/$REMOTE_HOSTNAME/chain.pem

if [[ $? -eq 0 ]];then
	echo "Renewing the certificate for $REMOTE_HOSTNAME"
	$DOCKER_COMPOSE run --rm certbot renew
	echo "Backup the fullchain cert to gitlab variable"
  /usr/bin/curl --show-error --silent --output /dev/null  \
      --request PUT --url "$CI_PROJECT_URL/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
      --form "environment_scope=$GIGADB_ENV" \
      --form "value=$(cat $FULLCHAIN_PEM)"

  echo "Backup the private cert to gitlab variable"
  /usr/bin/curl --show-error --silent --output /dev/null  \
      --request PUT --url "$CI_PROJECT_URL/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
      --form "environment_scope=$GIGADB_ENV" \
      --form "value=$(cat $PRIVATE_PEM)"

  echo "Backup the chain cert to gitlab variable"
  /usr/bin/curl --show-error --silent --output /dev/null  \
      --request PUT --url "$CI_PROJECT_URL/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
      --form "environment_scope=$GIGADB_ENV" \
      --form "value=$(cat $CHAIN_PEM)"
else
  echo "Certs do not exist in the filesystem"
  echo "To see if could be found in gitlab"
  http_code_get_fullchain=$(/usr/bin/curl --show-error --silent --output /dev/null --write-out "%{http_code}" \
    --request GET --url "$CI_PROJECT_URL/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
  )
  http_code_get_private=$(/usr/bin/curl --show-error --silent --output /dev/null --write-out "%{http_code}" \
    --request GET --url "$CI_PROJECT_URL/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
  )
  http_code_get_chain=$(/usr/bin/curl --show-error --silent --output /dev/null --write-out "%{http_code}" \
    --request GET --url "$CI_PROJECT_URL/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
  )

  if [[ $http_code_get_fullchain -eq 200 && $http_code_get_private -eq 200 && $http_code_get_chain -eq 200 ]];then
    echo "Certs fullchain, privkey and chain could be found in gitlab"
    echo "Get fullchain cert from gitlab"
    /usr/bin/curl --show-error --silent \
      --request GET --url "$CI_PROJECT_URL/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" | jq -r ".value" > /etc/letsencrypt/$GIGADB_ENV/$REMOTE_HOSTNAME/fullchain.pem

    echo "Get private cert from gitlab"
    /usr/bin/curl --show-error --silent \
      --request GET --url "$CI_PROJECT_URL/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" | jq -r ".value" > /etc/letsencrypt/$GIGADB_ENV/$REMOTE_HOSTNAME/privkey.pem

    echo "Get chain cert from gitlab"
    /usr/bin/curl --show-error --silent \
      --request GET --url "$CI_PROJECT_URL/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" | jq -r ".value" > /etc/letsencrypt/$GIGADB_ENV/$REMOTE_HOSTNAME/chain.pem
  fi

  if [[ $http_code_get_fullchain -eq 404 || $http_code_get_private -eq 404 || $http_code_get_chain -eq 404 ]];then
    echo "Not all certs found in gitlab, creating the certificate for $REMOTE_HOSTNAME!"
	  $DOCKER_COMPOSE run --rm certbot certonly -d $REMOTE_HOSTNAME
	  echo "Fullchain cert created and put it into gitlab"
    /usr/bin/curl --show-error --silent --output /dev/null \
      --request POST --url "$CI_PROJECT_URL/variables" \
      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
      --form "environment_scope=$GIGADB_ENV" \
      --form "key=tls_fullchain_pem" \
      --form "value=$(cat $FULLCHAIN_PEM)"

    echo "Private cert created and put it into gitlab"
    /usr/bin/curl --show-error --silent --output /dev/null \
      --request POST --url "$CI_PROJECT_URL/variables" \
      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
      --form "environment_scope=$GIGADB_ENV" \
      --form "key=tls_privkey_pem" \
      --form "value=$(cat $PRIVATE_PEM)"

    echo "Chain cert created and put it into gitlab"
    /usr/bin/curl --show-error --silent --output /dev/null \
      --request POST --url "$CI_PROJECT_URL/variables" \
      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
      --form "environment_scope=$GIGADB_ENV" \
      --form "key=tls_chain_pem" \
      --form "value=$(cat $CHAIN_PEM)"
  fi
fi