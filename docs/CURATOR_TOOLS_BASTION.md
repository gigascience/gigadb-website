# Using customized tools in the production bastion server

## Overview

![Tool Overview](./overview.png 'Overview of tools on bastion server')

The bastion server provides a set of command-line tools which implement the above workflow for ingesting Excel spreadsheets and performing post-upload operations.

## Logging into bastion server

Use your private key to SSH into the bastion server:
```
# Assumes your private key is located in /Users/<name>/.ssh directory
$ ssh -i ~/.ssh/key.pem <name>@bastion.gigadb.host
```

List tools:
```
[lily@ip-10-99-0-88 ~]$ ls /usr/local/bin/
__pycache__ calculateChecksumSizes compare createReadme datasetUpload docker-compose node_exporter postUpload rclone updateUrls wsdump.py
```

## Ingesting Excel spreadsheets into GigaDB

New datasets can be uploaded into GigaDB using Excel spreadsheets. Dataset metadata is added into [Excel template file version 19](https://github.com/gigascience/gigadb-website/blob/develop/gigadb/app/tools/excel-spreadsheet-uploader/template/GigaDBUpload-template_v19.xls). This Excel file needs to placed in the `uploadDir` directory:
```
# Your home directory can be referred to using ~
[lily@ip-10-99-0-88 ~]$ ls ~
uploadDir
```

Excel files can be uploaded into `uploadDir` using `sftp` tool or [Filezilla](https://filezilla-project.org). The Excel file can then be ingested:
```
[lily@ip-10-99-0-88 ~]$ datasetUpload
Done.
```

If the ingestion process has been successful then you should see the above output. In addition, the Excel file will have disappeared from `uploadDir` folder:
```
[lily@ip-10-99-0-88 ~]$ ls uploadDir/
```

Looking at the uploadDir/java.log will help confirm upload:
```
[lily@ip-10-99-0-88 ~]$ tail logs/java.log 
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

### Troubleshooting

If there is a problem with Excel file ingestion then you will see the following output when running `datasetUpload`:
```
$ ssh -i path/to/ssh.key centos@ ec2_bastion_public_ip
[lily@ip-10-99-0-88 ~]$ datasetUpload
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

In the above error, dataset_type is wrongly spelt as `Genomics` which breaks the ingestion process.

## Generating readme files

Readme files for datasets should be created changing directory to the user dropbox and calling createReadme and providing a DOI as a command-line parameter:
```
[lily@ip-10-99-0-88 ~]$ createReadme --doi 100142
```

A readme_<doi>.txt will appear in the user dropbox. The readme file will also have been uploaded into the correct dataset directory in Wasabi live bucket.

## Calculating MD5 checksum values and file sizes

Two files, doi.md5 and doi.filesizes are required to update these information in the database. These two files are created using the `calculateChecksumSizes` command. Firstly, change directory to the user drop box of the dataset then run `calculateChecksumSizes`:
```
# The files for dataset 100520 are being managed in user888 dropbox
[lily@ip-10-99-0-88 ~]$ cd /share/dropbox/user888
[lily@ip-10-99-0-88 user888]$ calculateChecksumSizes 100520
Created 100520.md5
Created 100520.filesizes
2024/05/07 08:22:59 INFO  : 100520.filesizes: Copied (new)
2024/05/07 08:22:59 INFO  : 
Transferred:             65 B / 65 B, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.3s

2024/05/07 08:23:00 INFO  : 100520.md5: Copied (new)
2024/05/07 08:23:00 INFO  : 
Transferred:            185 B / 185 B, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.3s
```

## Updating MD5 values and size information for dataset files in database

The `fileMetaToDb` can be called to update file metadata in the database:
```
[lily@ip-10-99-0-88 user103]$ filesMetaToDb 100142
```

You then need to check the dataset page in the file table to see if MD5 values and file sizes are visible.

## Using postUpload to create readme file and update file metadata in database

There is a script called postUpload which calls createReadme, calculateChecksumSizes and fileMetaToDb in turn so that these three tools do not have to be manually executed one after another. To use postUpload, change directory to the user dropbox folder that you are working on and run the postUpload script:
```
[lily@ip-10-99-0-88 ~]$ cd /share/dropbox/user888
[lily@ip-10-99-0-88 user888]$ postUpload 100142
[centos@ip-10-99-0-207 ~]$ ls -al ~/uploadDir/
-rw-rw-r--.  1 centos centos  668 Jun 17 04:41 readme_100142_20240617_044125.log
-rw-rw-r--.  1 centos centos   21 Jun 17 04:41 updating-file-size-100142.txt
-rw-rw-r--.  1 centos centos 1049 Jun 17 04:41 updating-md5checksum-100142.txt
[centos@ip-10-99-0-207 ~]$ more ~/uploadDir/readme_100142_20240617_044125.log
2024/06/17 04:41:29 INFO  : Created readme file for DOI 100142 in /usr/local/bin/runtime/curators/readme_100142.txt
2024/06/17 04:41:30 INFO  : readme_100142.txt: Copied (replaced existing)
2024/06/17 04:41:30 INFO  : Executed: rclone copy --s3-no-check-bucket /home/centos/readmeFiles/readme_100142.txt wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100142/ --config /home/centos/.config/rclone/rclone.conf
--log-file /home/centos/uploadLogs/readme_100142_20240617_044125.log --log-level INFO --stats-log-level DEBUG >> /home/centos/uploadDir/readme_100142_20240617_044125.log
2024/06/17 04:41:30 INFO  : Successfully copied file to Wasabi for DOI: 100142
```

## compare: How to compare files on the user dropbox with the files in the dataset spreadsheet

If there are discrepancies between the state of the filesystem in a user dropbox and the list of files in the dataset spreadsheet, it will cause errors in the processing of the dataset spreadsheet and the saving of files metadata to the database at a later stage of the process. It may be necessary to curate the actual files in user dropboxes for conformity to guidelines or organisational purposes.

To solve this problem, it is important to reconcile both sources of files list regularly. To help with that, there is command available on the bastion server, called compare, to compare the list of files in the dataset spreadsheet with the list of files on the filesystem. By default, when running the command, it will show any discrepancies in both direction.

### How to use the tool

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
