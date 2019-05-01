#!/bin/bash

echo "pure-pw useradd u-$1 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u uploader -d /home/uploader/$1  < /var/private/$1/upload_password.txt"
sudo docker exec tus-uppy-proto_ftpd_1 bash -c "pure-pw useradd u-$1 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u uploader -d /home/uploader/$1  < /var/private/$1/upload_password.txt"