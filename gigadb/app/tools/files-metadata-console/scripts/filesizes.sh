#!/bin/bash

# Place this script in dataset directory and run ./filesizes.sh

pwd=$(pwd)
filename=$(basename $pwd)

# Remove any existing *.tsv file
rm -f "$filename.tsv"

for i in $(find .  -type f ! -name 'filesizes.sh');
do
  # Create file containing dataset file sizes
  echo -e "$(stat -c '%s' "$i" | numfmt --to=iec --format=%2f)\t$i" >> "$filename.tsv"
done
echo -e "Created $filename.tsv file"

# For FTP server access from Tencent backup server
if test -f ~/.ftpconfig; then
  source ~/.ftpconfig
  curl -T "$filename.tsv" -u $ftp_credentials "$ftp_server/datasets/"
  echo -e "Uploaded $filename.tsv file to FTP server"
fi
