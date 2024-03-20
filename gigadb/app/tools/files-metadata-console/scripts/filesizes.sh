#!/bin/bash

# Place this script in dataset directory and run ./filesizes.sh

pwd=$(pwd)
filename=$(basename $pwd)

# Remove any existing *.tsv file
rm -f "$filename.tsv"

for i in $(find .  -type f ! -name 'filesizes.sh');
do
  # Create file containing dataset file sizes
  echo "$(wc -c $i)" >> "$filename.tsv"
done
echo -e "Created $filename.tsv file"

# Copy file into S3 bucket
aws s3 cp "$filename.tsv" s3://gigadb-datasets-metadata
