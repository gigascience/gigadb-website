#!/bin/bash

# Exit script on error
set -e

# How to use: Place this script in the dataset root directory
# Run ./md5.sh *
pwd=$(pwd)
filename=$(basename $pwd)

##############################################################
# Create doi.md5 file containing md5 checksum values for files
##############################################################

find .  -type f ! -name 'md5.sh' ! -name '*.filesizes' -exec md5sum {} \; > "$filename.md5"
echo "Created $filename.md5"

############################################################
# Create doi.filesizes file containing file size information
############################################################

FILESIZE_FILE="$filename.filesizes"
S3_BUCKET="s3://gigadb-datasets-metadata"

# Remove any existing filesizes file in current directory
rm -f "$FILESIZE_FILE"

for i in $(find .  -type f ! -name 'md5.sh' ! -name '*.filesizes');
do
  # Create file containing dataset file sizes
  echo -e "$(wc -c < $i)\t$i" >> "$FILESIZE_FILE"
done
echo "Created $FILESIZE_FILE"

# Copy files into S3 bucket
aws s3 cp "$FILESIZE_FILE" "$S3_BUCKET"
aws s3 cp "$filename.md5" "$S3_BUCKET"
