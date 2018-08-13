#!/usr/bin/env bash

set -e

source "./.env"

echo -e "POST /containers/${COMPOSE_PROJECT_NAME}_application_1/restart HTTP/1.0\r\n" | nc -vU /var/run/docker.sock