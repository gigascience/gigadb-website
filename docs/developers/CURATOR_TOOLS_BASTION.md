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
$ curl -L -o "./uploadDir/GigaDBUpload_v18_102498_TRR_202311_02_Cell_Clustering_Spatial_Transcriptomics.xls" "https://drive.google.com/uc?export=download&id=1sLabqRPkhF61nRocLmumjjCxxjzmODH5"
# Run Excel upload tool
$ ./execute.sh
RUN EXCEL SPREADSHEET TOOL
```

There are various checks which can be performed to determine if the Excel ingestion process was successful:

1. Check if the Excel file has disappeared from the upload directory. If the xls file is not there then this indicates the ingestion process succeeded.
2. Check the contents of the `logs/java.log` file. If the last line should look like this:
```
$ tail logs/java.log
>>>>>>>About to exec sqlTemp...
execution time: 84
**End success: GigaDBUpload_v18_102498_TRR_202311_02_Cell_Clustering_Spatial_Transcriptomics.xls
```
3. Go to http://gigadb.gigasciencejournal.com/adminDataset/update/id/2741 in your browser. This will take you to the dataset admin page for dataset 102498.

### Create readme file

After the Excel file has been ingested, then we are in the post upload stage of the dataset management process. The next step is to create the readme file for dataset 102498 using the information that has been uploaded into the database:
```
$ cd ../readme-generator
$ ./createReadme.sh --doi 102498 --outdir /home/curators
```

Check if the readme file has been created:
```
$ cat runtime/curators/readme_102498.txt
[DOI]
10.5524/102498

[Title]
Supporting data for "Foo's Novel Variable Neighborhood Search Approach for Cell Clustering for Spatial Transcriptomics"
```

The `readme_102498.txt` file needs to be placed into the user dropbox directory for dataset 102498 before proceeding to the next section. In the `dev` environment, this is simulated in the `files-metadata-console/tests/_data/dropbox/user5` directory:
```
$ cp runtime/curators/readme_102498.txt ../files-metadata-console/tests/_data/dropbox/user5
```

### Create dataset metadata md5 and filesizes files

This step of the post upload process involves updating the md5 checksum values and file sizes for dataset files. This information is provided by `$doi.md5` and `$doi.filesizes` files which can be generated as follows:
```
# Change directory to the 102498 user dropbox
$ cd ../files-metadata-console/tests/_data/dropbox/user5
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

Not only are the `102498.md5` and `102498.filesizes` files created in the user5 dropbox but also in the `/var/share/gigadb/metadata/` directory which is mapped onto `files-metadata-console/tests/_data/var/share/gigadb/metadata/` by the docker container. You can confirm this by looking in the `files-metadata-console/tests/_data/var/share/gigadb/metadata/` folder:
```
$ ls -l ../../../_data/var/share/gigadb/metadata/
total 20
-rw-r--r--  1 peterli  staff   89 Aug 26 19:41 102498.filesizes
-rw-r--r--  1 peterli  staff  178 Aug 26 19:41 102498.md5
```

### Update file size and md5 values in database for dataset 102498

The information contained within 102498.md5 and 102498.filesizes are used to update file size and md5 values in the database for dataset 102498 using the filesMetaToDb.sh script. It uses the files metadata console tool to update md5 values and file sizes from the md5 and filesizes files using the data from 102498.md5 and 102498.filesizes stored in the /var/share/gigadb/metadata/ directory:
```
$ cd ../../../../scripts
$ ./filesMetaToDb.sh 102498
Updating md5 checksum values as file attributes for 102498
Number of changes: 3
Updating file sizes for 102498
Number of changes: 3
Updated file metadata for 102498 in database
```

As you can see from the output, 3 files were updated with md5 values and file size which correspond to the 3 files listed in the .md5 and .filesizes files.

Another check is to look at the admin file pages in a browser. For example, looking at the file admin page for `DLPFC_69_72_VNS_results.csv` you will see it has the file sizes and md5 values seen in the 2 metadata files.

### Using postUpload.sh to perform post upload processing operations

There is a wrapper script called postUpload.sh which sequentially executes the above commands without having to call them in one after another. To confirm this script works, reset the database and ingest the Excel file:
```
# Spin up local instance of GigaDB
$ pwd
/path/to/gigadb-website
$ ./up.sh
# Download the Excel file for dataset 102498 into uploadDir folder
$ cd gigadb/app/tools/excel-spreadsheet-uploader
$ curl -L -o "./uploadDir/GigaDBUpload_v18_102498_TRR_202311_02_Cell_Clustering_Spatial_Transcriptomics.xls" "https://drive.google.com/uc?export=download&id=1sLabqRPkhF61nRocLmumjjCxxjzmODH5"
# Run Excel upload tool
$ ./execute.sh
RUN EXCEL SPREADSHEET TOOL
```

Now execute the post upload script:

```
$ cd gigadb/app/tools/files-metadata-console/scripts
$ ./postUpload.sh --doi 102498 --dropbox user5
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