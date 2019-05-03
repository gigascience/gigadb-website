#!/bin/bash

echo "pure-pw useradd d-$1 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u downloader -d /home/downloader/$1  < /var/private/$1/download_token.txt"
sudo docker exec tus-uppy-proto_ftpd_1 bash -c "pure-pw useradd d-$1 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u downloader -d /home/downloader/$1  < /var/private/$1/download_token.txt"