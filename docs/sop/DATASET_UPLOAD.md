# Upload dataset spreadsheets (DRAFT)

>Note: The examples below are for the **staging** environment. 
> When running on live environment, replace every occurrences of **staging** with **live** to get the commands to run.

## Prerequisite

Ensure the target environment is fully provisioned and that a complete pipeline has been run, built and deployed on the target environment.

## Summary

1. Sanity check the spreadsheet for obvious problems
2. Copy dataset spreadsheets to bastion
3. Run the spreadsheet uploader on bastion
4. Audit the upload process
5. Verify files urls validity
6. Generate MD5 checksums
7. Send a report to curators

## Detailed Process

### 1. Sanity check the spreadsheet for obvious problems

First, copy candidate dataset spreadsheets to your local ``uploadDir`` directory.
Then run the upload process locally:

```
$ cd gigadb/app/tools/excel-spreadsheet-uploader/
$ ./setup.sh
$ ./execute.sh
```

Verify that the uploadDir is empty and that ``java.log`` and ``javac.log`` have no errors.
Otherwise, check the spreadsheet for error and engage with the curators who sent it, until you get a version that's ingested successfully locally

### 2. Copy dataset spreadsheets to bastion


copy the candidate dataset spreadsheets to bastion server using SCP:

```
$ cd gigadb/app/tools/excel-spreadsheet-uploader/
$ scp -i <path to SSH key> uploadDir/GigaDB_v15_GIGA_D_22_00026_DeePVP_102240_v3.xls centos@13.38.58.174:/home/centos/uploadDir/
```

>Note: the remote ``/home/centos/uploadDir/`` directory is created during execution of the bastion playbook


### 3. Run the spreadsheet uploader on bastion

On your local environment:

```
$ cd ops/infrastructure/envs/staging
$ ansible -i ../../inventories name_bastion_server_staging_(lowercase IAM role here) -a "./datasetUpload.sh"
```

>Note 1: Alternatively you can ssh the bastion server and run the script directly from there.

>Note 2: the exact host name to use will be listed in the output of the command:
> ```
> $ cd ops/infrastructure/envs/staging
> $ ../../inventories/terraform-inventory.sh --list | jq
```

### 4. Audit the upload process


Verify that the ``/home/centos/uploadDir`` is empty and that ``/home/centos/uploadLogs/java.log`` and ``/home/centos/uploadLogs/javac.log`` have no errors.

```
$ cd ops/infrastructure/envs/staging
$ ansible -i ../../inventories name_bastion_server_staging_(lowercase IAM role here) -a "ls -al uploadDir"
$ ansible -i ../../inventories name_bastion_server_staging_(lowercase IAM role here) -a "cat uploadLogs/java.log"
$ ansible -i ../../inventories name_bastion_server_staging_(lowercase IAM role here) -a "cat uploadLogs/javac.log"
```

> Note: the uploader logs directory is located in ``/home/centos/uploadLogs``. That directory is created during execution of the bastion playbook

Navigate to the staging GigaDB website and generate a mockup for the DOI of the candidate spreadsheet.
Use it to sanity check related information like authors, samples, files, links, funding, etc...



>a. On the gigadb website for the target environment, navigate to the admin dataset update form for the dataset that's been uploaded
> 
>b. Check that the dataset specific information is correct
> 
>c. Click on "Create/Reset Private URL" to generate a private mockup url
> 
>d. This will lead you to the private mockup page where you can check the associated information (files, samples, funding, links, ...)
> 

### 5. Verify files urls validity


```
$ cd ops/infrastructure/envs/staging
$ ansible -i ../../inventories name_bastion_server_staging_(lowercase IAM role here) -a "docker run -e YII_PATH=/var/www/vendor/yiisoft/yii -it registry.gitlab.com/gigascience/forks/<your project prefix>-gigadb-website/production_app:staging ./protected/yiic files checkUrls --doi=(insert DOI here)"

```

If the script has any output, and the command's response code is 0 (``rc=0``), then there's no known problems.

### 6. Generate MD5 checksums

The MD5 values for a dataset's files come from a `<dataset_doi>.md5` file which 
is available from the CNGB FTP server via its URL. This file must be present in 
order for MD5 file attributes to be added into the database for the dataset.
```
# Use bastion server to run command:
$ cd ops/infrastructure/envs/staging
$ ansible -i ../../inventories name_bastion_server_staging_<lowercase IAM role here> -a "docker run -e YII_PATH=/var/www/vendor/yiisoft/yii -it registry.gitlab.com/gigascience/forks/<your project prefix>-gigadb-website/production_app:staging ./protected/yiic files updateMD5FileAttributes --doi=<insert DOI here>"
```

### 7. Send a report to curators

Send the link to the mockup page to curators and the list of problem file urls if any.