# Using customized tools in the production bastion server

## Overview

![Tool Overview](https://www.dropbox.com/scl/fi/8jl4kg8w39f2l0qjujdga/overview.png?rlkey=azi9chfz1w496iuunilzmtsls&dl=0 'Overview of tools on bastion server')

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
[lily@ip-10-99-0-88 user103]$ cd /share/dropbox/user888
[lily@ip-10-99-0-88 user103]$ calculateChecksumSizes 100520
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

