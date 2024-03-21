#!/bin/bash

# Exit script on error
set -e

# How to use: Place this script in the dataset root directory
# Run ./md5.sh *
pwd=$(pwd)
filename=$(basename $pwd)

MD5_FILE="$filename.md5"
FILESIZE_FILE="$filename.filesizes"
S3_BUCKET="s3://gigadb-datasets-metadata"

# Create doi.md5 file containing md5 checksum values for files
find .  -type f ! -name "$MD5_FILE" ! -name "$FILESIZE_FILE" -exec md5sum {} \; > "$MD5_FILE"
echo "Created $MD5_FILE"

# Create doi.filesizes file containing file size information
for i in $(find .  -type f ! -name "$MD5_FILE" ! -name "$FILESIZE_FILE");
do
  echo -e "$(wc -c < $i)\t$i" >> "$FILESIZE_FILE"
done
echo "Created $FILESIZE_FILE"

# In case we are on GigaDB file server
if [[ $(uname -n) =~ cngb-gigadb-ftp ]];then
  export AWS_CONFIG_FILE=/etc/aws/config
  export AWS_SHARED_CREDENTIALS_FILE=/etc/aws/credentials
fi
  
# Copy files into S3 bucket
aws s3 cp "$FILESIZE_FILE" "$S3_BUCKET"
aws s3 cp "$MD5_FILE" "$S3_BUCKET"
