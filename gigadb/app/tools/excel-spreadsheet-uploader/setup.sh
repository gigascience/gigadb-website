#!/usr/bin/env bash

# Download java source files
curl -L -O https://github.com/gigascience/ExceltoGigaDB/archive/develop.zip

# Unpack source files in place
bsdtar -k --strip-components=1 -xvf develop.zip

echo "excel-spreadsheet-uploader is set up"
