#!/bin/bash

# Exit script on error
set -e

if [[ -z "$1" ]]; then
    echo "Error: DOI is required!"
    echo "Usage: calculate_checksum_sizes.sh <DOI>"
    exit 1
fi

doi="$1"

MD5_FILE="$doi.md5"
FILESIZE_FILE="$doi.filesizes"
S3_BUCKET="gigadb-datasets-metadata"

echo $MD5_FILE
echo $FILESIZE_FILE

# Create doi.md5 file containing md5 checksum values for files
find .  -type f ! -name "$MD5_FILE" ! -name "$FILESIZE_FILE" -exec md5sum {} \; > "$MD5_FILE"
echo "Created $MD5_FILE"

# Create doi.filesizes file containing file size information
for i in $(find .  -type f ! -name "$MD5_FILE" ! -name "$FILESIZE_FILE");
do
  echo -e "$(wc -c < $i)\t$i" >> "$FILESIZE_FILE"
done
echo "Created $FILESIZE_FILE"

# In case we are on the bastion
if [[ $(uname -n) =~ compute ]];then
  rclone copy -v "$FILESIZE_FILE" s3_metadata:"S3_BUCKET"
  rclone copy -v "$MD5_FILE" s3_metadata:"S3_BUCKET"
fi