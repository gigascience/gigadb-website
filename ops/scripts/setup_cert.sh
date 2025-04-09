#!/usr/bin/env bash

# bail out if an unset variable is used
set -u

# bail out as soon as there is an error
set -e

echo "Starting create/renew tls certs at $(date +%Y-%m-%dT%H:%M:%S)"

# configure docker cmd
if [[ $(uname -n) =~ compute ]];then
  echo "Running on productions, using docker"
  source "/home/ec2-user/.tls-certs-secrets"
	DOCKER_CMD="docker run --rm -v ${REPO_NAME}_le_config:/etc/letsencrypt -v ${REPO_NAME}_assets:/var/www/assets registry.gitlab.com/$CI_PROJECT_PATH/production_config:$GIGADB_ENV"
else
  echo "Running on non-productions, using docker-compose"
  source "./.env"
  source "./.secrets"
	DOCKER_CMD="docker-compose --tlsverify -H=$REMOTE_WEBAPP_DOCKER -f ops/deployment/docker-compose.production-envs.yml run --rm config"
fi

# Path to the certs
FULLCHAIN_PEM=/etc/letsencrypt/archive/$REMOTE_HOSTNAME/fullchain1.pem
PRIVATE_PEM=/etc/letsencrypt/archive/$REMOTE_HOSTNAME/privkey1.pem
CHAIN_PEM=/etc/letsencrypt/archive/$REMOTE_HOSTNAME/chain1.pem

FULLCHAIN_LINK=/etc/letsencrypt/live/$REMOTE_HOSTNAME/fullchain.pem
PRIVATE_LINK=/etc/letsencrypt/live/$REMOTE_HOSTNAME/privkey.pem
CHAIN_LINK=/etc/letsencrypt/live/$REMOTE_HOSTNAME/chain.pem

# Definition of functions
renew_cert() {
    echo "Read content of files"
    fullchain=$($DOCKER_CMD cat $FULLCHAIN_PEM)
    privkey=$($DOCKER_CMD cat $PRIVATE_PEM)
    chain=$($DOCKER_CMD cat $CHAIN_PEM)

  	echo "Renewing the certificate for $REMOTE_HOSTNAME"
  	docker run --rm certbot/certbot renew
  	echo "Backup the fullchain cert to gitlab variable"
  	if [ $fullchain_pem_remote_exists == "true" ];then
  	  echo "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=\$fullchain'"
      $DOCKER_CMD bash -c "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=$fullchain'"
  	else
  	  echo "/usr/bin/curl --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_fullchain_pem' --form 'value=\$fullchain'"
      $DOCKER_CMD bash -c "/usr/bin/curl -L --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_fullchain_pem' --form 'value=$fullchain'"
  	fi
    echo "Backup the private key to gitlab variable"
  	if [ $privkey_pem_remote_exists == "true" ];then
  	  echo "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=\$privkey'"
      $DOCKER_CMD bash -c "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=$privkey'"
  	else
  	  echo "/usr/bin/curl --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_privkey_pem' --form 'value=\$privkey'"
      $DOCKER_CMD bash -c "/usr/bin/curl -L --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_privkey_pem' --form 'value=$privkey'"
  	fi
  	echo "Backup the chain cert to gitlab variable"
  	if [ $chain_pem_remote_exists == "true" ];then
  	  echo "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=\$chain'"
      $DOCKER_CMD bash -c "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=$chain'"
  	else
  	  echo "/usr/bin/curl --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_chain_pem' --form 'value=\$chain'"
      $DOCKER_CMD bash -c "/usr/bin/curl -L --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_chain_pem' --form 'value=$chain'"
  	fi
}

fetch_cert_from_gitlab() {
    echo "Making the directories to store the certificate files"
    $DOCKER_CMD mkdir -vp /etc/letsencrypt/archive/$REMOTE_HOSTNAME
    $DOCKER_CMD mkdir -vp /etc/letsencrypt/live/$REMOTE_HOSTNAME
    echo "Get fullchain cert from gitlab"
    $DOCKER_CMD bash -c "/usr/bin/curl --show-error --silent \
      --request GET --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' \
      --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' | cat | jq -r '.value' > /etc/letsencrypt/archive/$REMOTE_HOSTNAME/fullchain1.pem"
    $DOCKER_CMD ln -fs /etc/letsencrypt/archive/$REMOTE_HOSTNAME/fullchain1.pem /etc/letsencrypt/live/$REMOTE_HOSTNAME/fullchain.pem

    echo "Get private cert from gitlab"
    $DOCKER_CMD bash -c "/usr/bin/curl --show-error --silent \
      --request GET --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' \
      --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' | cat | jq -r '.value' > /etc/letsencrypt/archive/$REMOTE_HOSTNAME/privkey1.pem"
    $DOCKER_CMD ln -fs /etc/letsencrypt/archive/$REMOTE_HOSTNAME/privkey1.pem /etc/letsencrypt/live/$REMOTE_HOSTNAME/privkey.pem

    echo "Get chain cert from gitlab"
    $DOCKER_CMD bash -c "/usr/bin/curl --show-error --silent \
      --request GET --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' \
      --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' | cat | jq -r '.value' > /etc/letsencrypt/archive/$REMOTE_HOSTNAME/chain1.pem"
    $DOCKER_CMD ln -fs /etc/letsencrypt/archive/$REMOTE_HOSTNAME/chain1.pem /etc/letsencrypt/live/$REMOTE_HOSTNAME/chain.pem

    $DOCKER_CMD ls -alrt /etc/letsencrypt/archive/$REMOTE_HOSTNAME
    $DOCKER_CMD ls -alrt /etc/letsencrypt/live/$REMOTE_HOSTNAME

}

make_new_cert() {
    echo "Running certbot to make new cert"
    docker run --rm certbot/certbot certonly -d $REMOTE_HOSTNAME -d portainer.$REMOTE_HOSTNAME
    echo "Read content of files"
    $DOCKER_CMD mkdir -vp /etc/letsencrypt/archive/$REMOTE_HOSTNAME
    $DOCKER_CMD mkdir -vp /etc/letsencrypt/live/$REMOTE_HOSTNAME
    fullchain=$($DOCKER_CMD cat $FULLCHAIN_PEM)
    privkey=$($DOCKER_CMD cat $PRIVATE_PEM)
    chain=$($DOCKER_CMD cat $CHAIN_PEM)
    echo "And then backup the newly created cert to GitLab"
    echo "/usr/bin/curl --show-error --silent --request POST --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_fullchain_pem' --form 'value=$fullchain'"
    $DOCKER_CMD bash -c "/usr/bin/curl --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_fullchain_pem' --form 'value=$fullchain'"
    $DOCKER_CMD bash -c "/usr/bin/curl --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_privkey_pem' --form 'value=$privkey'"
    $DOCKER_CMD bash -c "/usr/bin/curl --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_chain_pem' --form 'value=$chain'"
}

echo "Checking whether the certificate exists locally"
cert_files_local_exists=$($DOCKER_CMD bash -c "test -f $FULLCHAIN_PEM && test -f $PRIVATE_PEM && test -f $CHAIN_PEM && echo 'true' || echo 'false'")
echo "cert_files_local_exists: $cert_files_local_exists"

encoded_gitlab_project=$(echo $CI_PROJECT_PATH | sed -e 's/\//%2F/g')

echo "To see if they could be found in gitlab"
tls_fullchain_pem_response_code=$(curl --silent --output /dev/null --write-out "%{http_code}" --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV")
if [[ $tls_fullchain_pem_response_code == 200 ]];then
  fullchain_pem_remote_exists="true"
else
  fullchain_pem_remote_exists="false"
fi

tls_privkey_pem_response_code=$(curl --silent --output /dev/null --write-out "%{http_code}" --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV")
if [[ $tls_privkey_pem_response_code == 200 ]];then
  privkey_pem_remote_exists="true"
else
  privkey_pem_remote_exists="false"
fi

tls_chain_pem_response_code=$(curl --silent --output /dev/null --write-out "%{http_code}" --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV")
if [[ $tls_chain_pem_response_code == 200 ]];then
  chain_pem_remote_exists="true"
else
  chain_pem_remote_exists="false"
fi

echo "fullchain_pem_remote_exists: $fullchain_pem_remote_exists"
echo "privkey_pem_remote_exists: $privkey_pem_remote_exists"
echo "chain_pem_remote_exists: $chain_pem_remote_exists"

if [[ $cert_files_local_exists == 'true' ]];then
  renew_cert
else
  echo "Certs do not exist in the filesystem"
  if [[ $fullchain_pem_remote_exists == "true" && $privkey_pem_remote_exists == "true" && $chain_pem_remote_exists == "true" ]];then
    echo "Certs fullchain, privkey and chain could be found in gitlab"
    fetch_cert_from_gitlab
    echo "now that the cert files are present locally, lets renew them"
    renew_cert
  else
    echo "No certs on GitLab, certbot to create one"
    make_new_cert
  fi
fi

echo "Finishing create/renew tls certs at $(date +%Y-%m-%dT%H:%M:%S)"
