#!/usr/bin/env bash

# bail out if an unset variable is used
#set -u

# bail out as soon as there is an error
set -e

# Load environment variables
source "./.env"
source "./.secrets"

# Path to the certs
FULLCHAIN_PEM=/etc/letsencrypt/archive/$REMOTE_HOSTNAME/fullchain1.pem
PRIVATE_PEM=/etc/letsencrypt/archive/$REMOTE_HOSTNAME/privkey1.pem
CHAIN_PEM=/etc/letsencrypt/archive/$REMOTE_HOSTNAME/chain1.pem

# docker-compose executable
if [[ $GIGADB_ENV != "dev" && $GIGADB_ENV != "CI" ]];then
	DOCKER_COMPOSE="docker-compose --tlsverify -H=$REMOTE_DOCKER_HOST -f ops/deployment/docker-compose.production-envs.yml"
else
	DOCKER_COMPOSE="docker-compose"
fi

echo "Checking whether the certificate exists locally"
cert_files_local_exists=$($DOCKER_COMPOSE run --rm config /bin/bash -c "test -f $FULLCHAIN_PEM && test -f $PRIVATE_PEM && test -f $CHAIN_PEM && echo 'true' || echo 'false'")
echo "cert_files_local_exists: $cert_files_local_exists"
echo "To see if they could be found in gitlab"
if ! [ -z "$tls_fullchain_pem" ];then
  fullchain_pem_remote_exists="true"
else
  fullchain_pem_remote_exists="false"
fi

if ! [ -z "$tls_privkey_pem" ];then
  privkey_pem_remote_exists="true"
else
  privkey_pem_remote_exists="false"
fi

if ! [ -z "$tls_chain_pem" ];then
  chain_pem_remote_exists="true"
else
  chain_pem_remote_exists="false"
fi


if [[ $cert_files_local_exists == 'true' ]];then
	echo "Renewing the certificate for $REMOTE_HOSTNAME"
	$DOCKER_COMPOSE run --rm certbot renew
#	echo "Backup the fullchain cert to gitlab variable"
#	if [ $fullchain_pem_remote_exists == "true" ];then
#    $DOCKER_COMPOSE run --rm config bash -c "/usr/bin/curl --show-error --silent --request PUT --url '$CI_PROJECT_URL/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=@$FULLCHAIN_PEM'"
#  else
#    $DOCKER_COMPOSE run --rm config bash -c "/usr/bin/curl -L --show-error --silent --request POST --url '$CI_PROJECT_URL/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_fullchain_pem' --form 'value=@$FULLCHAIN_PEM'"
#  fi
#  echo "Backup the private cert to gitlab variable"
#  if [ $privkey_pem_remote_exists == "true" ];then
#    $DOCKER_COMPOSE run --rm config bash -c "/usr/bin/curl --show-error --silent --request PUT --url '$CI_PROJECT_URL/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=@$FULLCHAIN_PEM'"
#  else
#    $DOCKER_COMPOSE run --rm config bash -c "/usr/bin/curl -L --show-error --silent --request POST --url '$CI_PROJECT_URL/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_privkey_pem' --form 'value=@$FULLCHAIN_PEM'"
#  fi
#
#  echo "Backup the chain cert to gitlab variable"
#  if [ $chain_pem_remote_exists == "true" ];then
#    $DOCKER_COMPOSE run --rm config bash -c "/usr/bin/curl --show-error --silent --request PUT --url '$CI_PROJECT_URL/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=@$FULLCHAIN_PEM'"
#  else
#    $DOCKER_COMPOSE run --rm config bash -c "/usr/bin/curl -L --show-error --silent --request POST --url '$CI_PROJECT_URL/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_chain_pem' --form 'value=@$FULLCHAIN_PEM'"
#  fi

else
  echo "Certs do not exist in the filesystem"
  $DOCKER_COMPOSE run --rm certbot certonly -d $REMOTE_HOSTNAME

#  if [[ $fullchain_remote_exists -eq 200 && $privkey_remote_exists -eq 200 && $chain_remote_exists -eq 200 ]];then
#    echo "Certs fullchain, privkey and chain could be found in gitlab"
#    echo "Get fullchain cert from gitlab"
#    $DOCKER_COMPOSE run --rm config /usr/bin/curl --show-error --silent \
#      --request GET --url "$CI_PROJECT_URL/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
#      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" | jq -r ".value" > $FULLCHAIN_PEM
#
#    echo "Get private cert from gitlab"
#    $DOCKER_COMPOSE run --rm config /usr/bin/curl --show-error --silent \
#      --request GET --url "$CI_PROJECT_URL/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
#      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" | jq -r ".value" > $PRIVATE_PEM
#
#    echo "Get chain cert from gitlab"
#    $DOCKER_COMPOSE run --rm config /usr/bin/curl --show-error --silent \
#      --request GET --url "$CI_PROJECT_URL/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV" \
#      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" | jq -r ".value" > $CHAIN_PEM
#  fi
#
#  if [[ $fullchain_remote_exists -eq 404 || $privkey_remote_exists -eq 404 || $chain_remote_exists -eq 404 ]];then
#    echo "Not all certs found in gitlab, creating the certificate for $REMOTE_HOSTNAME!"
#	  $DOCKER_COMPOSE run --rm certbot certonly -d $REMOTE_HOSTNAME
#	  echo "Fullchain cert created and put it into gitlab"
#    $DOCKER_COMPOSE run --rm config /usr/bin/curl --show-error --silent --output /dev/null \
#      --request POST --url "$CI_PROJECT_URL/variables" \
#      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
#      --form "environment_scope=$GIGADB_ENV" \
#      --form "key=tls_fullchain_pem" \
#      --form "value=$(cat $FULLCHAIN_PEM)"
#
#    echo "Private cert created and put it into gitlab"
#    $DOCKER_COMPOSE run --rm config /usr/bin/curl --show-error --silent --output /dev/null \
#      --request POST --url "$CI_PROJECT_URL/variables" \
#      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
#      --form "environment_scope=$GIGADB_ENV" \
#      --form "key=tls_privkey_pem" \
#      --form "value=$(cat $PRIVATE_PEM)"
#
#    echo "Chain cert created and put it into gitlab"
#    $DOCKER_COMPOSE run --rm config /usr/bin/curl --show-error --silent --output /dev/null \
#      --request POST --url "$CI_PROJECT_URL/variables" \
#      --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" \
#      --form "environment_scope=$GIGADB_ENV" \
#      --form "key=tls_chain_pem" \
#      --form "value=$(cat $CHAIN_PEM)"
#  fi
fi