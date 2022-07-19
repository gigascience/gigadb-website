#!/usr/bin/env bash

# Make an environment file
if [[ ! -f .env ]];then
  cp ./env-default .env
fi

PATH=/usr/local/bin:$PATH
export PATH

# Download java source files
docker-compose run --rm uploader curl -L -O https://github.com/gigascience/ExceltoGigaDB/archive/develop.zip

# Unpack source files in place
docker-compose run --rm uploader bsdtar -k --strip-components=1 -xvf develop.zip


