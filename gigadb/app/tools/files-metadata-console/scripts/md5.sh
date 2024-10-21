#!/bin/bash

# Exit script on error
set -e

# Check if DOI is provided
if [[ -z "$1" ]]; then
    echo "Error: DOI is required!"
    echo "Usage: $0 <DOI>"
    echo "Calculates MD5 checksums values and file sizes for a given DOI."
    exit 1
fi

# Set DOI and file names
doi="$1"
MD5_FILE="$doi.md5"
FILESIZE_FILE="$doi.filesizes"

# Create doi.md5 file containing md5 checksum values for files
find .  -type f ! -name "$MD5_FILE" ! -name "$FILESIZE_FILE" -exec md5sum {} \; > "$MD5_FILE"
echo "Created $MD5_FILE"

# Create out.txt file containing file size information
find . -type f ! -name "$MD5_FILE" ! -name "$FILESIZE_FILE" ! -name out.txt -exec wc -c {} \; > out.txt
# Transform any space delimiters in out.txt into tabs
tr " " "\t" < out.txt > "$FILESIZE_FILE"
rm out.txt
echo "Created $FILESIZE_FILE"
