#!/usr/bin/env bash

set -e
set -u

# Download the files from /datasetSubmission/upload using curl

mkdir "tests/_data/upload"

curl --url "http://gigadb.gigasciencejournal.com:9170/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx" --output "tests/_data/upload/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"
curl --url "http://gigadb.gigasciencejournal.com:9170/files/templates/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx" --output "tests/_data/upload/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx"
curl --url "http://gigadb.gigasciencejournal.com:9170/files/templates/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx" --output "tests/_data/upload/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx"

# Check the existence of the files
# Expected files and their path
path="tests/_data/upload/"
TemplateFile="GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"
ExampleFile1="GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx"
ExampleFile2="GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx"

TemplateFileExists=0
ExampleFile1Exists=0
ExampleFile2Exists=0

if [ -f "${path}$TemplateFile" ]; then
  TemplateFileExists=1
  echo "$TemplateFile is downloaded!"
else
  echo "$TemplateFile is not found"
fi

if [ -f "${path}$ExampleFile1" ]; then
  ExampleFile1Exists=1
  echo "$ExampleFile1 is downloaded!"
else
  echo "$ExampleFile1 is not found"
fi

if [ -f "${path}$ExampleFile2" ]; then
  ExampleFile2Exists=1
  echo "$ExampleFile2 is downloaded!"
else
  echo "$ExampleFile2 is not found"
fi

if [ $TemplateFileExists -eq 1 ] && [ $ExampleFile1Exists -eq 1 ] && [ $ExampleFile2Exists -eq 1 ]; then
    echo "All files are downloadable, the test download folder will be deleted!"
    rm -rf $path
else
    echo "Not all files are downloaded, please check again!!!!!!"
fi