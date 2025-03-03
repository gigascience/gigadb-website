#!/usr/bin/env bash

set -e

touch ./new_dropboxes.txt
date >> ./new_dropboxes.txt
for username in "$@"; do
  echo "Generating password for user $username:"
  password=$(tr -dc A-Za-z0-9 </dev/urandom | head -c 13; echo)
  echo $password > /tmp/$username.pass
  echo $password >> /tmp/$username.pass
  echo "$username:$password" >> ./new_dropboxes.txt
  echo "Making directory in /share/dropbox"
  mkdir -p /share/dropbox/$username
  echo "Creating FTP account"
  docker compose exec -T ftpd pure-pw useradd $username -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u dropboxuser -d /home/$username < /tmp/$username.pass
  echo "Cleaning up"
  rm /tmp/$username.pass
done