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
TemplateFile="tests/_data/upload/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"
ExampleFile1="tests/_data/upload/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx"
ExampleFile2="tests/_data/upload/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx"

fileCounts=0

if [ -f "$TemplateFile" ]; then
  ((fileCounts+=1))
  echo "$TemplateFile is downloaded!"
else
  echo "$TemplateFile is not found"
fi

if [ -f "$ExampleFile1" ]; then
  ((fileCounts+=1))
  echo "$ExampleFile1 is downloaded!"
else
  echo "$ExampleFile1 is not found"
fi

if [ -f "$ExampleFile2" ]; then
  ((fileCounts+=1))
  echo "$ExampleFile2 is downloaded!"
else
  echo "$ExampleFile2 is not found"
fi

if [ "$fileCounts" -eq 3 ]; then
  echo "All files are downloadable, the test download folder will be deleted!"
  rm -rf "tests/_data/upload"
else
  echo "Not all files are downloaded, please check again!!!!!!"
fi