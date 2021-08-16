#!/usr/bin/env bash

# bail out if an unset variable is used
set -u


# Load .env se we can access CSV_DIR
source "./.env"

# Path to the certs and store the cert content
FULLCHAIN_PEM=$(cat /etc/letsencrypt/live/$REMOTE_HOSTNAME/fullchain.pem)
PRIVATE_PEM=$(cat /etc/letsencrypt/live/$REMOTE_HOSTNAME/privkey.pem)
CHAIN_PEM=$(cat /etc/letsencrypt/live/$REMOTE_HOSTNAME/chain.pem)

# Backup the fullchain cert to GITLAB CI environment variable and get the http code
http_code_fullchain=$(curl --include --show-error --silent --output /dev/null --write-out "%{http_code}" \
    --request POST --url "$PROJECT_VARIABLES_URL" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
    --header "content-type: application/json" \
    --data '{
        "key": "'"$GIGADB_ENV"'_tlsauth_fullchain",
        "value": "'"$(echo $FULLCHAIN_PEM)"'",
        "environment_scope": "'"$GIGADB_ENV"'"
      }'
    )

if [[ $http_code_fullchain -eq 400 ]];then
  curl --include --include --show-error --silent --output /dev/null  \
    --request PUT --url "$PROJECT_VARIABLES_URL/${GIGADB_ENV}_tlsauth_fullchain" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
    --header "content-type: application/json" \
    --data '{
        "value": "'"$(echo $FULLCHAIN_PEM)"'",
        "environment_scope": "'"$GIGADB_ENV"'"
      }'
fi
# Backup the private key cert to GITLAB CI environment variable and get the http code
http_code_private=$(curl --include --show-error --silent --output /dev/null --write-out "%{http_code}" \
    --request POST --url "$PROJECT_VARIABLES_URL" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
    --header "content-type: application/json" \
    --data '{
        "key": "'"$GIGADB_ENV"'_tlsauth_private",
        "value": "'"$(echo $PRIVATE_PEM)"'",
        "environment_scope": "'"$GIGADB_ENV"'"
      }'
    )

if [[ $http_code_private -eq 400 ]];then
  curl --include --show-error --silent --output /dev/null  \
    --request PUT --url "$PROJECT_VARIABLES_URL/${GIGADB_ENV}_tlsauth_private" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
    --header "content-type: application/json" \
    --data '{
        "value": "'"$(echo $PRIVATE_PEM)"'",
        "environment_scope": "'"$GIGADB_ENV"'"
      }'
fi
# Backup the chain cert to GITLAB CI environment variable and get the http code
http_code_chain=$(curl --include --show-error --silent --output /dev/null --write-out "%{http_code}" \
    --request POST --url "$PROJECT_VARIABLES_URL" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
    --header "content-type: application/json" \
    --data '{
        "key": "'"$GIGADB_ENV"'_tlsauth_fullchain",
        "value": "'"$(echo $CHAIN_PEM)"'",
        "environment_scope": "'"$GIGADB_ENV"'"
      }'
    )

if [[ $http_code_chain -eq 400 ]];then
  curl --include --show-error --silent --output /dev/null  \
    --request PUT --url "$PROJECT_VARIABLES_URL/${GIGADB_ENV}_tlsauth_chain" \
    --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
    --header "content-type: application/json" \
    --data '{
        "value": "'"$(echo $CHAIN_PEM)"'",
        "environment_scope": "'"$GIGADB_ENV"'"
      }'
fi

# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.production-envs.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

echo "Checking whether the certificate exists"
$DOCKER_COMPOSE exec -T web test -f /etc/letsencrypt/live/$REMOTE_HOSTNAME/fullchain.pem

if [[ $? -eq 0 ]];then
	echo "Renewing the certificate for $REMOTE_HOSTNAME"
	$DOCKER_COMPOSE run --rm certbot renew
else
	echo "Creating the certificate for $REMOTE_HOSTNAME"
	$DOCKER_COMPOSE run --rm certbot certonly -d $REMOTE_HOSTNAME
fi