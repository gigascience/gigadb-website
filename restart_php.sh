#!/usr/bin/env bash

set -e

source "./.env"

APPLICATION_CONTAINER=$(echo -e "GET /containers/json HTTP/1.0\r\n" | nc -U /var/run/docker.sock | awk 'NR==1,/^\r$/ {next} {printf "%s%s",$0,RT}' | jq -r '.[] | select(.Labels."com.docker.compose.service" == "application") | .Names | .[0]' )

echo -e "POST /containers${APPLICATION_CONTAINER}/restart HTTP/1.0\r\n" | nc -vU /var/run/docker.sock