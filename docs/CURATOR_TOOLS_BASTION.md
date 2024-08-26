# Using customized tools in the production bastion server

## Overview

![Tool Overview](./overview.png 'Overview of tools on bastion server')

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
# Run md5.sh using file metadata console container - reqiires DOI as a command line parameter
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

## Staging and live environment

### Logging into bastion server

Use your private key to SSH into the bastion server:
```
# Assumes your private key is located in /Users/<name>/.ssh directory
$ ssh -i ~/.ssh/id-openssh-bastion-peterl.pem peterl@bastion.gigadb.host
Activate the web console with: systemctl enable --now cockpit.socket

Last login: Fri Jul 26 11:59:36 2024 from 116.49.85.85
[peterl@ip-10-99-0-88 ~]$
```

List tools:
```
[peterl@ip-10-99-0-88 ~]$ ls /usr/local/bin/
__pycache__ calculateChecksumSizes compare createReadme datasetUpload docker-compose node_exporter postUpload rclone updateUrls wsdump.py
```

### Ingesting Excel spreadsheets into GigaDB

New datasets are uploaded into GigaDB using Excel spreadsheets. Dataset metadata is added into [Excel template file version 19](https://github.com/gigascience/gigadb-website/blob/develop/gigadb/app/tools/excel-spreadsheet-uploader/template/GigaDBUpload-template_v19.xls). This Excel file needs to placed in the `uploadDir` directory:
```
# Your home directory can be referred to using ~
[peterl@ip-10-99-0-88 ~]$ ls ~
uploadDir
```

Excel files can be uploaded into `uploadDir` using `sftp` tool or [Filezilla](https://filezilla-project.org). The Excel file can then be ingested using the datasetUpload script in `/usr/local/bin`:
```
[peterl@ip-10-99-0-88 ~]$ sudo /usr/local/bin/datasetUpload
Done.
```

If the ingestion process has been successful then you should see the above output. In addition, the Excel file will have disappeared from `uploadDir` folder and there will be two log files:
```
[peterl@ip-10-99-0-88 ~]$ ls uploadDir/
java.log  javac.log
```

Looking at the uploadDir/java.log will help confirm upload:
```
[peterl@ip-10-99-0-88 ~]$ tail uploadDir/java.log 
Insert True: insert into file_attributes select 674771, 538926, 1045, 'S10', null where not exists ( select null from file_attributes where id = 674771 ); 
Insert false: insert into file_attributes select 674771, 538926, 1045, 'S10', null where not exists ( select null from file_attributes where id = 674771 ); 
Insert True: insert into file_attributes select 674772, 538927, 1045, 'S12', null where not exists ( select null from file_attributes where id = 674772 ); 
Insert false: insert into file_attributes select 674772, 538927, 1045, 'S12', null where not exists ( select null from file_attributes where id = 674772 ); 
Insert True: insert into file_attributes select 674773, 538928, 1045, 'S11', null where not exists ( select null from file_attributes where id = 674773 ); 
Insert false: insert into file_attributes select 674773, 538928, 1045, 'S11', null where not exists ( select null from file_attributes where id = 674773 ); 
>>>>>>>About to exec sqlTemp...
execution time: 168
**End success: GigaDBUpload_v18_GIGA-D-23-00109-Koref4K-updated.xls
```

You should also check the corresponding dataset admin page at http://gigadb.gigasciencejournal.com/adminDataset/update/id/<dataset_id> which you will be able to find by entering the dataset's DOI into the column header in dataset admin page.

Also, look at the dataset's samples and files in the relevant dataset samples and dataset files admin pages.

#### Troubleshooting

If there is a problem with Excel file ingestion then you will see the following output when running `datasetUpload`:
```
[peterl@ip-10-99-0-88 ~]$ datasetUpload
Spreadsheet cannot be uploaded, please check logs!
Done.
```

Do as the output message suggests by checking `tail uploadDir/java.log`:
```
publisher test OK? true
contentXXX: Genomics
target: dataset_type
content: Genomics
values: [Genomic, Metagenomic, Epigenomic, Proteomic, Transcriptomic, Metabolomic, Neuoscience, Bioinformatics, Workflow, Software, Imaging, Network-Analysis, Genome-Mapping, ElectroEncephaloGraphy(EEG), Metadata, Metabarcoding, Virtual-Machine, Climate, Ecology, Lipidomic, Phenotyping]
relation test OK? false
email test OK? false
attribute_id test OK? false
author_name test OK? false
project test OK? false
image test OK? false
file_type test OK? false
latest date   2024-3-6
Finished validation OK? false
End error 1: GigaDBUpload_v18_GIGA-D-23-00109-Koref4K.xls
fillTable output: true
validation output: false
[GigaDBUpload_v18_GIGA-D-23-00109-Koref4K.xls]
```

In the above example error, dataset_type is wrongly spelt as `Genomics` which breaks the ingestion process and therefore needs to be corrected.

### Generating readme files

From your home directory on the bastion server, readme files for datasets can be created using the `createReadme` script by calling it with a DOI; `--wasabi --apply --use-live-data` are parameters required to copy the readme file into Wasabi:
```
[peterl@ip-10-99-0-142 ~]$ pwd
/home/peterl
[peterl@ip-10-99-0-88 ~]$ sudo /usr/local/bin/createReadme --doi 100142 --wasabi --apply --use-live-data
```

A readme_<doi>.txt will appear in the `uploadDir` directory.
```
[peterl@ip-10-99-0-142 ~]$ ls uploadDir/
java.log  javac.log  readme_100142.txt  readme_100142_20240727_042600.log
```
The readme file will also have been uploaded into the correct dataset directory in Wasabi live bucket.  The file size and MD5 value for the readme file will also be updated in the database.

### Calculating MD5 checksum values and file sizes

> [!WARNING]
> Executing `calculateChecksumSizes` as a curator user on bastion is currently broken. Since `calculateChecksumSizes` is called using sudo, it will look for /root/.config/rclone/rclone.conf that is not created. There is a `~/.config/rclone/rclone.conf` created each curator user's home directory and it is this config file that should be used in `calculateChecksumSizes` when copying doi.md5 and doi.filesizes files into S3 bucket. Actually, we can stop copying these files to S3 bucket and have `fileMetaToDb` use the files in the dropbox directory.
```
[peterl@ip-10-99-0-142 user888]$ sudo /usr/local/bin/calculateChecksumSizes 100888
Created 100888.md5
Created 100888.filesizes
2024/07/27 05:39:21 NOTICE: Config file "/root/.config/rclone/rclone.conf" not found - using defaults
2024/07/27 05:39:21 Failed to create file system for "aws_metadata:gigadb-datasets-metadata": didn't find section in config file
```

Two files, doi.md5 and doi.filesizes are required to update MD5 checksum values and file sizes for dataset files in the database. These two files are created using the `calculateChecksumSizes` command. Firstly, change directory to the user drop box of the dataset then run `calculateChecksumSizes`, providing it with the DOI of the dataset as a paramter:
```
# The files for dataset 100888 are being managed in user888 dropbox
[peterl@ip-10-99-0-88 ~]$ cd /share/dropbox/user888
[peterl@ip-10-99-0-88 user888]$ sudo /usr/local/bin/calculateChecksumSizes 100888
Created 100888.md5
Created 100888.filesizes
2024/05/07 08:22:59 INFO  : 100888.filesizes: Copied (new)
2024/05/07 08:22:59 INFO  : 
Transferred:             65 B / 65 B, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.3s

2024/05/07 08:23:00 INFO  : 100888.md5: Copied (new)
2024/05/07 08:23:00 INFO  : 
Transferred:            185 B / 185 B, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.3s
```

### Updating MD5 values and size information for dataset files in database

The `fileMetaToDb` can be called to update file metadata in the database:
```
[peterl@ip-10-99-0-88 ~]$ sudo /usr/local/bin/filesMetaToDb 100142

* About to update files' MD5 Checksum as file attribute for 100142
Saved md5 file attribute with id: 17050
Saved md5 file attribute with id: 17053
Saved md5 file attribute with id: 17052

Done with updating files' MD5 Checksum as file attribute for 100142. Process status is saved in file: /home/centos/uploadLogs/updating-md5checksum-100142.txt

* About to update files' size for 100142
Number of changes: 0

Done with updating files' size for 100142. Nb of successful changes saved in file: /home/centos/uploadLogs/updating-file-size-100142.txt

Logs for updating md5 values and file sizes have been moved to: /home/peterl/uploadDir

Update files meta data to database done!

```

You then need to check the dataset page in the file table to see if MD5 values and file sizes are visible.

### Using postUpload to create readme file and update file metadata in database

There is a script called postUpload which calls createReadme, calculateChecksumSizes and fileMetaToDb in turn so that these three tools do not have to be manually executed one after another:
```
[peter@ip-10-99-0-88 ~]$ $ sudo /usr/local/bin/postUpload 100142

* About to create the README file for 100142
[DOI]
10.5524/100142

[Title]
Supporting scripts and data for "Investigation into the annotation of protocol sequencing steps in the Sequence Read Archive".

[Release Date]
2015-05-11

[Citation]
Alnasir, J; Shanahan, H (2015): Supporting scripts and data for "Investigation into the annotation of protocol sequencing steps in the Sequence Read Archive".
GigaScience Database. https://dx.doi.org/10.5524/100142

[Dataset Type]
Metadata

[Dataset Summary]
The workflow for the production of high-throughput sequencing data from nucleic acid samples is complex. There are a series of protocol steps to be followed in the preparation of samples for next-generation sequencing.  The quantification of bias in a number of protocol steps, namely DNA fractionation, blunting, phosphorylation, adapter ligation and library enrichment, remains to be determined. 
We examined the experimental metadata of the public repository Sequence Read Archive (SRA) in order to ascertain the level of annotation of important sequencing steps in submissions to the database. 

[File name] - [File Description] - [File Location]
SRAmetadb.zip - Archival copy of SRA metadata at time of experiment - https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/SRAmetadb.zip
readme_100142.txt -  - https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/staging/pub/10.5524/100001_101000/100142/readme_100142.txt
Diagram-ALL-FIELDS-Check-annotation.jpg - image usedin manuscript - https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/Diagram-ALL-FIELDS-Check-annotation.jpg
Diagram-SRA-Study-Experiment-Joined-probing.jpg -  - https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/Diagram-SRA-Study-Experiment-Joined-probing.jpg

[License]
All files and data are distributed under the CC0 1.0 Universal (CC0 1.0) Public Domain Dedication (https://creativecommons.org/publicdomain/zero/1.0/), unless specifically stated otherwise, see http://gigadb.org/site/term for more details.

[Comments]

[End]

The readme_100142.txt has been moved to: /home/peterl/uploadDir

Log for copying readme_100142.txt to wasabi bucket has been moved to: /home/peterl/uploadDir
Created readme file and uploaded it to Wasabi gigadb-website/staging bucket directory

Done with creating the README file for 100142.

* About to update files' size and MD5 checksum for 100142

* About to update files' MD5 Checksum as file attribute for 100142
Saved md5 file attribute with id: 17050
Saved md5 file attribute with id: 17053
Saved md5 file attribute with id: 17052

Done with updating files' MD5 Checksum as file attribute for 100142. Process status is saved in file: /home/centos/uploadLogs/updating-md5checksum-100142.txt

* About to update files' size for 100142
Number of changes: 0

Done with updating files' size for 100142. Nb of successful changes saved in file: /home/centos/uploadLogs/updating-file-size-100142.txt

Logs for updating md5 values and file sizes have been moved to: /home/peterl/uploadDir

Update files meta data to database done!

Done with updating files' size and MD5 checksum for 100142.
```
> [!WARNING]
> It is strange that the `Number of changes: 0` for `About to update files' size for 100142`. I suspect there is a mismatch between the delimiter used to separate file size values and file names in doi.filesizes, and the delimiter used to parse these lines in the file size update tool.

### compare: How to compare files on the user dropbox with the files in the dataset spreadsheet

If there are discrepancies between the state of the filesystem in a user dropbox and the list of files in the dataset spreadsheet, it will cause errors in the processing of the dataset spreadsheet and the saving of files metadata to the database at a later stage of the process. It may be necessary to curate the actual files in user dropboxes for conformity to guidelines or organisational purposes.

To solve this problem, it is important to reconcile both sources of files list regularly. To help with that, there is command available on the bastion server, called compare, to compare the list of files in the dataset spreadsheet with the list of files on the filesystem. By default, when running the command, it will show any discrepancies in both direction.

#### How to use the tool

Open the dataset spreadsheet you are working on the files list tab

Copy the list of files and paste it into a text file that you save as DOI_xls.txt (where DOI is to be replaced by the real DOI of the dataset you are working on

Upload the text file listing all the files form the spreadsheet to the bastion server in your home directory using your preferred method (FileZilla, scp, …)

Connect to the bastion with SSH

Change directory to the user dropbox associated with the dataset spreadsheet you are working on
```
$ cd /share/dropbox/user0
```

Remember where you have saved the file DOI_xls.txt ? Pass it as the first argument to the compare command, and pass the current directory (.) as the second argument
```
$ compare /home/rija/100006_xls.txt .
```

If there are any discrepancies between the files listed in the spreadsheet and the files in the user dropbox, that command will output them.

> The command won’t show the list of files, it only output differences in either directions. If you want to see the full list of files from the spreadsheet and have highlighted the ones that are missing in the user dropbox, pass the -v parameter as the final argument to the command:
```
$ compare /home/rija/100006_xls.txt . -v
```
