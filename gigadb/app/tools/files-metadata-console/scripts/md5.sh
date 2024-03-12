#!/bin/bash

# How to use: Place this script in the dataset root directory
# Run ./md5.sh *
pwd=$(pwd)
filename=$(basename $pwd)
find .  -type f -exec md5sum {} \; > "$filename.md5"

# For FTP server access from Tencent backup server
if test -f ~/.ftpconfig; then
  source ~/.ftpconfig
  curl -T "$filename.md5" -u $ftp_credentials "$ftp_server/datasets/"
  echo -e "Uploaded $filename.md5 file to FTP server"
fi
