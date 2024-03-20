#!/bin/bash

# Place this script in dataset directory and run ./filesizes.sh

pwd=$(pwd)
filename=$(basename $pwd)

FILESIZE_FILE="$filename.filesizes"
S3_BUCKET="s3://gigadb-datasets-metadata"

# Remove any existing filesizes file for dataset
rm -f "$FILESIZE_FILE"

for i in $(find .  -type f ! -name 'filesizes.sh');
do
  # Create file containing dataset file sizes
  echo "$(wc -c $i)" >> "$FILESIZE_FILE"
done
echo -e "Created $FILESIZE_FILE file"

# Copy file into S3 bucket
aws s3 cp "$FILESIZE_FILE" "$S3_BUCKET"
