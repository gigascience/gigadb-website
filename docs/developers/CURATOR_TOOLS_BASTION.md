# Using customized tools in the production bastion server

## Overview

![Tool Overview](../curators/overview.png 'Overview of tools on bastion server')

The bastion server provides a set of command-line tools which implement the above workflow for ingesting Excel spreadsheets and performing post-upload operations.

## Test curator tools in dev environment

### Ingest dataset Excel file

Information about a dataset is provided within an Excel file which is ingested by the Excel upload tool. This Excel file needs to be placed into the `excel-spreadsheet-uploader/uploadDir` directory before it can be uploaded into GigaDB.
```
# Spin up local instance of GigaDB
$ pwd
/path/to/gigadb-website
$ ./up.sh
# Download the Excel file for dataset 102498 into uploadDir folder
$ cd gigadb/app/tools/excel-spreadsheet-uploader
$ curl -L -o "./uploadDir/GigaDBUpload_v18_102498_TRR_202311_02_Cell_Clustering_Spatial_Transcriptomics.xls" "https://drive.google.com/uc?export=download&id=129j3ikdSojNVpvZPnBefoOA2Uz6OusHR"
# Run Excel upload tool
$ ./execute.sh
RUN EXCEL SPREADSHEET TOOL
```

There are various checks which can be performed to determine if the Excel ingestion process was successful:

1. Check if the Excel file has disappeared from the uploadDir directory. If the xls file is not there then this indicates the ingestion process succeeded.
2. Check the contents of the `logs/java.log` file. If the last line should look like this:
```
$ tail logs/java.log
>>>>>>>About to exec sqlTemp...
execution time: 84
**End success: GigaDBUpload_v18_102498_TRR_202311_02_Cell_Clustering_Spatial_Transcriptomics.xls
```
3. Go to http://gigadb.gigasciencejournal.com/adminDataset/update/id/2741 in your browser. This will take you to the dataset admin page for dataset 102498.

### Create readme file

After the Excel file has been ingested, then we are in the post upload stage of the dataset management process. To simulate the user dropbox for dataset 102498, go to the directory below: 
```
$ cd ../files-metadata-console/tests/_data/dropbox/user5
```

Now create the readme file for dataset 102498 using the information that has been uploaded into the database:
```
$ ../../../../../readme-generator/createReadme.sh --doi 102498
```

Check if the readme file has been created:
```
$ cat readme_102498.txt
[DOI]
10.5524/102498

[Title]
Supporting data for "Foo's Novel Variable Neighborhood Search Approach for Cell Clustering for Spatial Transcriptomics"
```

### Create dataset metadata md5 and filesizes files

This step of the post upload process involves updating the md5 checksum values and file sizes for dataset files. This information is provided by `102498.md5` and `102498.filesizes` files which can be generated as follows:
```
# Run md5.sh using file metadata console container - requires DOI as a command line parameter
$ docker-compose run --rm -w /gigadb/app/tools/files-metadata-console/tests/_data/dropbox/user5 files-metadata-console ../../../../scripts/md5.sh 102498
Created 102498.md5
Created 102498.filesizes
```

Check the contents of the two files:
```
$ more 102498.filesizes
301     ./DLPFC_69_72_VNS_results.csv
332     ./E2_VNS_Ground_Truth.csv
5123    ./readme_102498.txt

$ more 102498.md5
dc1feb8af3b8c02b0b615e968b87786d  ./DLPFC_69_72_VNS_results.csv
b5a7e0953d1581077c13818153371918  ./E2_VNS_Ground_Truth.csv
44c4c953d70d5194f920f3ade6a317f7  ./readme_102498.txt
```


### Update file size and md5 values in database for dataset 102498

The information contained within 102498.md5 and 102498.filesizes are used to update file size and md5 values in the database for dataset 102498 using the filesMetaToDb.sh script:
```
$ ../../../../scripts/filesMetaToDb.sh 102498
Updating md5 checksum values as file attributes for 102498
Number of changes: 3
Updating file sizes for 102498
Number of changes: 3
Updated file metadata for 102498 in database
```

As you can see from the output, 3 files were updated with md5 values and file sizes which correspond to the 3 files listed in the .md5 and .filesizes files.

Another check is to look at the admin file pages: http://gigadb.gigasciencejournal.com/adminFile/admin. For example, looking at the file admin page for `DLPFC_69_72_VNS_results.csv` you will see it has the file sizes and md5 values seen in the 2 metadata files.

### Using postUpload.sh to perform post upload processing operations

There is a wrapper script called `postUpload.sh` which sequentially executes the above commands without having to call them one after another. To confirm this script works, reset the database and ingest the Excel file:
```
# Spin up local instance of GigaDB
$ pwd
/path/to/gigadb-website
$ ./up.sh
# Download the Excel file for dataset 102498 into uploadDir folder
$ cd gigadb/app/tools/excel-spreadsheet-uploader
$ curl -L -o "./uploadDir/GigaDBUpload_v18_102498_TRR_202311_02_Cell_Clustering_Spatial_Transcriptomics.xls" "https://drive.google.com/uc?export=download&id=129j3ikdSojNVpvZPnBefoOA2Uz6OusHR"
# Run Excel upload tool
$ ./execute.sh
RUN EXCEL SPREADSHEET TOOL
```

Now execute the post upload script:

```
$ cd ../files-metadata-console/tests/_data/dropbox/user5
# Remove files generated by tools in current user5 directory
$ git clean -x -f
# Execute post upload script
$ ../../../../scripts/postUpload.sh --doi 102498 --dropbox user5
Creating README file for 102498
Copying README file into dropbox user5
Creating dataset metadata files for 102498
Created 102498.md5
Created 102498.filesizes
Updating file sizes and MD5 values in database for 102498
Updating md5 checksum values as file attributes for 102498
Number of changes: 3
Updating file sizes for 102498
Number of changes: 3
Updated file metadata for 102498 in database
```

### Bats test for post upload script

There is a bats test for the postUpload.sh which enacts the above steps. The bats test can be executed as follows:
```
$ cd gigadb/app/tools/files-metadata-console/tests/bats
$ bats postUpload.bats 
 ✓ Test postUpload.sh
   Executing setup code
   Downloading Excel file for dataset 102498
   Ingesting Excel file for dataset 102498
   Executing test postUpload.sh --doi 102498 --dropbox user5
   Executing teardown code

1 test, 0 failures
```