#!/bin/bash

set -e

# determine the name of the container that run pure-ftpd
FTPD_CONTAINER=$(sudo /usr/bin/curl -s --unix-socket /var/run/docker.sock -H "Content-Type: application/json" http:/v1.37/containers/json | jq -r '.[] | select(.Labels."com.docker.compose.service" == "ftpd") | .Names | .[0]')

# create a resource on Docker API for the command to run on the ftpd container
BODY="{\"AttachStdin\": false, \"AttachStdout\": true, \"AttachStderr\": true, \"Tty\": false, \"Cmd\": [\"bash\",\"-c\",\"/usr/bin/pure-pw useradd d-$1 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u downloader -d /home/downloader/$1  < /var/private/$1/download_token.txt\"] }"
echo $BODY > /tmp/payload_download_$1.json
EXEC_ID=$(sudo /usr/bin/curl -s --unix-socket /var/run/docker.sock -H "Content-Type: application/json" -d @/tmp/payload_download_$1.json http:/v1.37/containers${FTPD_CONTAINER}/exec | jq -r ".Id")

# Run the command to create an ftp account on the ftpd container
sudo /usr/bin/curl -s --unix-socket /var/run/docker.sock -H "Content-Type: application/json" -d @/tmp/payload_download_$1.json http:/v1.37/exec/${EXEC_ID}/start
