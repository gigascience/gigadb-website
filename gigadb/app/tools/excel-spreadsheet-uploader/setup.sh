#!/usr/bin/env bash

# Make an environment file
if [[ -f ./env-bastion ]];then
  cp ./env-bastion .env
else
  cp ./env-default .env
fi


# Download java source files
docker-compose run --rm uploader curl -L -O https://github.com/gigascience/ExceltoGigaDB/archive/develop.zip

# Unpack source files in place
docker-compose run --rm uploader bsdtar -k --strip-components=1 -xvf develop.zip


