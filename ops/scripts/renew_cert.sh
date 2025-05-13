#!/usr/bin/env bash

# bail out if an unset variable is used
set -u

# bail out as soon as there is an error
set -e

echo -e "Starting renew certificate at $(date +%Y-%m-%dT%H:%M:%S)\n"

# configure docker cmd
if [[ $(uname -n) =~ compute ]];then
  echo "Running on productions, using docker"
  source "/home/ec2-user/.tls-certs-secrets"
	DOCKER="docker run --rm -v ${REPO_NAME}_le_config:/etc/letsencrypt -v ${REPO_NAME}_assets:/var/www/assets registry.gitlab.com/$CI_PROJECT_PATH/production_config:$GIGADB_ENV"
fi

# Path to the certs
FULLCHAIN_PEM=/etc/letsencrypt/archive/$REMOTE_HOSTNAME/fullchain1.pem
PRIVATE_PEM=/etc/letsencrypt/archive/$REMOTE_HOSTNAME/privkey1.pem
CHAIN_PEM=/etc/letsencrypt/archive/$REMOTE_HOSTNAME/chain1.pem

FULLCHAIN_LINK=/etc/letsencrypt/live/$REMOTE_HOSTNAME/fullchain.pem
PRIVATE_LINK=/etc/letsencrypt/live/$REMOTE_HOSTNAME/privkey.pem
CHAIN_LINK=/etc/letsencrypt/live/$REMOTE_HOSTNAME/chain.pem

# Renew detection directory
renew_detection_dir=/home/ec2-user/cert_renew_detection

# Definition of functions
renew_cert() {
    echo "Renewing the certificate for $REMOTE_HOSTNAME"
  	docker run --rm -v $renew_detection_dir:/renew_detect -v ${REPO_NAME}_le_config:/etc/letsencrypt -v ${REPO_NAME}_le_webrootpath:/var/www/.le certbot/certbot renew --deploy-hook "touch /renew_detect/cert_renewed_successfully"

  	if [[ -f $renew_detection_dir/cert_renewed_successfully ]]; then
  	  echo -e "Certificate for $REMOTE_HOSTNAME renewed successfully!\n"

  	  echo -e "Restart the web container\n"
  	  docker restart ${REPO_NAME}_web_1
  	  restart_status=$?

  	  if [[ $restart_status -eq 0 ]]; then
  	    echo -e "Web container restarted successfully!\n"
  	    echo -e "Backup the fullchain cert to gitlab variable\n"
        echo -e "Read content of files\n"
        fullchain=$($DOCKER cat $FULLCHAIN_PEM)
        privkey=$($DOCKER cat $PRIVATE_PEM)
        chain=$($DOCKER cat $CHAIN_PEM)

        if [ $fullchain_pem_remote_exists == "true" ];then
          echo "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=\$fullchain'"
          $DOCKER bash -c "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_fullchain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=$fullchain'"
        else
          echo "/usr/bin/curl --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_fullchain_pem' --form 'value=\$fullchain'"
          $DOCKER bash -c "/usr/bin/curl -L --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_fullchain_pem' --form 'value=$fullchain'"
        fi
        echo "Backup the private key to gitlab variable"
        if [ $privkey_pem_remote_exists == "true" ];then
          echo "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=\$privkey'"
          $DOCKER bash -c "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_privkey_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=$privkey'"
        else
          echo "/usr/bin/curl --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_privkey_pem' --form 'value=\$privkey'"
          $DOCKER bash -c "/usr/bin/curl -L --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_privkey_pem' --form 'value=$privkey'"
        fi
        echo "Backup the chain cert to gitlab variable"
        if [ $chain_pem_remote_exists == "true" ];then
          echo "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=\$chain'"
          $DOCKER bash -c "/usr/bin/curl --show-error --silent --request PUT --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables/tls_chain_pem?filter%5benvironment_scope%5d=$GIGADB_ENV' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'value=$chain'"
        else
          echo "/usr/bin/curl --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_chain_pem' --form 'value=\$chain'"
          $DOCKER bash -c "/usr/bin/curl -L --show-error --silent --request POST --write-out 'HTTP Response code: %{http_code}' --url '$CI_API_V4_URL/projects/$encoded_gitlab_project/variables' --header 'PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN' --form 'environment_scope=$GIGADB_ENV' --form 'key=tls_chain_pem' --form 'value=$chain'"
        fi

        echo -e "Symlinks the new certs\n"
        $DOCKER ln -fs $FULLCHAIN_PEM $FULLCHAIN_LINK
        $DOCKER ln -fs $PRIVATE_PEM $PRIVATE_LINK
        $DOCKER ln -fs $CHAIN_PEM $CHAIN_LINK
      else
        echo -e " Web container could not be restarted!\n"
        exit 1
      fi
    else
      echo -e "Certificate for $REMOTE_HOSTNAME was not renewed!\n"
    fi
}

echo "Checking whether the certbot is configured correctly in local"
certbot_configured_correctly=$(docker run --rm -v ${REPO_NAME}_le_config:/etc/letsencrypt -v ${REPO_NAME}_le_webrootpath:/var/www/.le certbot/certbot certificates 2>&1 | grep -q $REMOTE_HOSTNAME && echo 'true' || echo 'false')
echo -e "certbot_configured_correctly: $certbot_configured_correctly\n"

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

if [[ $certbot_configured_correctly == 'true' ]];then
  renew_cert
else
  echo -e "Certbot is not configured correctly!\n"
  exit 1
fi
