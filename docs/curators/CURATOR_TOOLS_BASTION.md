# Using customized tools in the production bastion server

## Overview

![Tool Overview](./overview.png 'Overview of tools on bastion server')

New datasets are uploaded into GigaDB using Excel spreadsheets. The bastion server provides a set of command-line tools which implement the above workflow for ingesting Excel spreadsheets and performing post-upload operations.

## 1. datasetUpload

After you have logged into the bastion server (bastion.gigadb.host) using SSH, you can begin the process of Excel spreadsheet ingestion into GigaDB.

Dataset metadata is added into [Excel template file version 19](https://github.com/gigascience/gigadb-website/blob/develop/gigadb/app/tools/excel-spreadsheet-uploader/template/GigaDBUpload-template_v19.xls). This Excel file needs to placed in the `uploadDir` directory:
```
# Your home directory can be referred to using ~
[peterl@ip-10-99-0-88 ~]$ ls ~
uploadDir
```

Excel files can be uploaded into `uploadDir` using `sftp` tool or [Filezilla](https://filezilla-project.org).

> [!TIP]
> For testing purposes, download a test Excel file into `uploadDir` using this command: `curl -L -o "./uploadDir/GigaDBUpload_v18_102498_TRR_202311_02_Cell_Clustering_Spatial_Transcriptomics.xls" "https://drive.google.com/uc?export=download&id=129j3ikdSojNVpvZPnBefoOA2Uz6OusHR"`

The Excel file can then be ingested using the datasetUpload script in `/usr/local/bin`:
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
Insert false: insert into file_attributes select 674872, 538971, 572, 'MIT', null where not exists ( select null from file_attributes where id = 674872 ); 
>>>>>>>About to exec sqlTemp...
execution time: 130
**End success: GigaDBUpload_v18_102498_TRR_202311_02_Cell_Clustering_Spatial_Transcriptomics.xls
```

You should also check the corresponding dataset admin page at `https://gigadb.org/adminDataset/update/id/<dataset_id>` which you will be able to find by entering the dataset's DOI, e.g. 102498 into the DOI column header in /adminDataset/admin page.

Also, look at the dataset's samples and files in the relevant dataset samples and dataset files admin pages.

> [!TIP]
> If there is a problem with Excel file ingestion then you will see the following output when running `datasetUpload`:
```
[peterl@ip-10-99-0-88 ~]$ datasetUpload
Spreadsheet cannot be uploaded, please check logs!
Done.
```

> Do as the output message suggests by checking `tail uploadDir/java.log`:
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

> In the above example error, dataset_type is wrongly spelt as `Genomics` which breaks the ingestion process and therefore needs to be corrected.

## 2. Change directory to /share/dropbox/user directory

Each dataset has an associated user dropbox directory located at `/share/dropbox/` that contains the files belonging to the dataset. Change directory to this user drop box directory, for example:
```
$ cd /share/dropbox/user5
```

## 3. createReadme

From this the user dropbox directory, a readme file for the dataset can be created using the `createReadme` script by calling it with a DOI; `--wasabi --apply --use-live-data` are parameters required to copy the readme file into Wasabi:
```
[peterl@ip-10-99-0-142 ~]$ pwd
/share/dropbox/user5
[peterl@ip-10-99-0-88 ~]$ sudo /usr/local/bin/createReadme --doi 102498
```

A `readme_<doi>.txt` file will appear in `/share/dropbox/user5` directory.
```
[peterl@ip-10-99-0-142 ~]$ ls
DLPFC_69_72_VNS_results.csv  E2_VNS_Ground_Truth.csv  readme_102498.txt
```
The readme file will also have been uploaded into the correct dataset directory in Wasabi live bucket.  The file size and MD5 value for the readme file will also be updated in the database.

## 4. calculateChecksumSizes

`$doi.md5` and `$doi.filesizes` provide information used to update dataset files with md5 values and file size in the database. These two files can be generated from the user5 dropbox:
```
# Provide DOI number as a parameter
[peterl@ip-10-99-0-95 user5]$ sudo /usr/local/bin/calculateChecksumSizes 102498
Created 102498.md5
Created 102498.filesizes
```

Check the contents of the two files:
```
[peterl@ip-10-99-0-95 user5]$ more 102498.filesizes 
5124    ./readme_102498.txt
301     ./DLPFC_69_72_VNS_results.csv
332     ./E2_VNS_Ground_Truth.csv

[peterl@ip-10-99-0-95 user5]$ more 102498.md5 
2b74aa5af1b67e48f0317748cbfdf310  ./readme_102498.txt
dc1feb8af3b8c02b0b615e968b87786d  ./DLPFC_69_72_VNS_results.csv
b5a7e0953d1581077c13818153371918  ./E2_VNS_Ground_Truth.csv
```

## 5. Run `filesMetdaToDb` to update file with md5 values and sizes in database

The `fileMetaToDb` script can use `102498.filesizes` and `102498.md5` to update file metadata in the database from the user dropbox folder:
```
[peterl@ip-10-99-0-95 ~]$ sudo /usr/local/bin/filesMetaToDb 102498
Updating md5 checksum values as file attributes for 102498
Number of changes: 3
Updating file sizes for 102498
Number of changes: 3
Updated file metadata for 102498 in database
```

You should check the adminfile pages of the files associated with this dataset to see if MD5 values and file sizes are visible.

## 6. Go to dataset admin page on gigadb.org

With the post upload operations complete, you need to go back to the page at https://gigadb.org/adminDataset/update/id/<dataset_id>` in order to continue curation work on the dataset. You will be able to find this link by entering the dataset's DOI, e.g. 102498 into the DOI column header in /adminDataset/admin page.

## `postUpload`: a wrapper script to create readme file and update file metadata in database

There is a script called `postUpload` which calls `createReadme`, `calculateChecksumSizes` and `fileMetaToDb` in turn so that these three tools do not have to be manually executed one after another:
```
# Ensure you are in the dropbox directory
[peterl@ip-10-99-0-88 ~]$ pwd
/share/dropbox/user5
[peterl@ip-10-99-0-88 ~]$ sudo /usr/local/bin/postUpload --doi 102498 --dropbox user5
Creating README file for 102498
[DOI]
10.5524/102498
...
[Comments]

[End]
Created readme file and uploaded it to Wasabi gigadb-website/staging bucket directory
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
> [!TIP]
> Take note of the number of changes made by the md5 and file size update tool. This number should be equal to the number of files listed in the metadata files.

To ensure the postUpload script has worked, you should perform checks using the dataset, sample and file admin pages to see if dataset metadata are correctly stored in the database.

## compare: How to compare files on the user dropbox with the files in the dataset spreadsheet

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
