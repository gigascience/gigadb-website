#!/bin/bash

# Exit script on error
set -e

# How to use: Place this script in the dataset root directory
# Run ./md5.sh *
pwd=$(pwd)
filename=$(basename $pwd)
find .  -type f -exec md5sum {} \; > "$filename.md5"
echo -e "Created $filename.md5"

############################################################
# Create doi.filesizes file containing file size information
############################################################

FILESIZE_FILE="$filename.filesizes"
S3_BUCKET="s3://gigadb-datasets-metadata"

# Remove any existing filesizes file in current directory
rm -f "$FILESIZE_FILE"

for i in $(find .  -type f ! -name 'filesizes.sh');
do
  # Create file containing dataset file sizes
  echo "$(wc -c $i)" >> "$FILESIZE_FILE"
done
echo -e "Created $FILESIZE_FILE"

# Copy files into S3 bucket
aws s3 cp "$FILESIZE_FILE" "$S3_BUCKET"
aws s3 cp "$filename.md5" "$S3_BUCKET"
