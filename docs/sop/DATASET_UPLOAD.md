# Upload dataset spreadsheets (DRAFT)

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
scp -i <path to SSH key> GigaDB_v15_GIGA_D_22_00026_DeePVP_102240_v3.xls centos@13.38.58.174:/home/centos/uploadDir/
```

>Note: the remote ``/home/centos/uploadDir/`` directory is created during execution of the bastion playbook


### 3. Run the spreadsheet uploader on bastion

SSH to the bastion server

```
$ terraform output
...
ec2_bastion_public_ip = "15.236.110.52"
...
$ ssh -i <path to SSH key> centos@15.236.110.52 
``` 

Run the upload process on bastion

```
$ cd /home/centos
$ ./datasetUpload.sh
```


### 4. Audit the upload process


Verify that the ``/home/centos/uploadDir`` is empty and that ``/home/centos/uploadLogs/java.log`` and ``/home/centos/uploadLogs/javac.log`` have no errors.

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
$ docker run -e YII_PATH=/var/www/vendor/yiisoft/yii -it registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_app:staging ./protected/yiic files checkUrls --doi=(insert doi here)
$ echo $?
```

If the script has any output, and the command's response status is 0, then there's no known type of file urls problems.

### 6. Generate MD5 checksums

TODO

### 7. Send a report to curators

Send the link to the mockup page to curators and the list of problem file urls if any.