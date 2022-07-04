# SOP: Uploading a dataset using spreadsheet tool

New datasets can be uploaded into GigaDB for the curators using the spreadsheet 
tool.

## Prerequisite

Ensure cloud deployment is made and fully working for the target environment.

## Process

0. Sanity check the spreadsheet for obvious problems

1. SSH to the bastion server
```
$ terraform output
...
ec2_bastion_public_ip = "15.236.110.52"
...
$ ssh -i /Users/owner/.ssh/aws-gigadb-eu-west-3-Rija.pem centos@15.236.110.52 
``` 

2. Change to the spreadsheet uploader tool and setup the tool
```
$ cd /home/centos/gigadb-website-develop/gigadb/app/tools/excel-spreadsheet-uploader/
$ ./setup.sh
$ rm uploadDir/100679newversion.xls
```

That will download and install in place the source code for the java tool and 
remove the test dataset spreadsheet. Ensure that an ``.env`` file containing 
connection details to the database has been created

3. Place the dataset spreadsheet to upload at the location where it can be 
processed

You can do so by:

a. Copy the dataset spreadsheet to upload in the `uploadDir`` directory of your local environment ``gigadb/app/tools/excel-spreadsheet-uploader/uploadDir/``
b. From your local environment,  transfer that file to the bastion server using 
an Ansible task
```
$ cd ops/infrastructure/envs/<your environment>
$ ansible-playbook -i ../../inventories bastion_playbook.yml --tags "spreadsheet"
```

c. Verify on the bastion server that the file is the correct location
```
$ cd /home/centos/gigadb-website-develop/gigadb/app/tools/excel-spreadsheet-uploader/
$ ls -alrt uploadDir
```

4. Execute the dataset spreadsheet uploader tool
```
$ cd /home/centos/gigadb-website-develop/gigadb/app/tools/excel-spreadsheet-uploader/
$ ./execute.sh
```

If successful, the spreadsheet is removed from ``uploadDir``. If not successful,
check first ``javac.log`` to see if it's a code problem. If no error there, 
check ``java.log`` for operational error.

5. Verify the result of the upload on GigaDB website

a. On the gigadb website for the target environment, navigate to the admin 
dataset update form for the dataset that's been uploaded

b. Check that the dataset specific information is correct

c. Click on "Create/Reset Private URL" to generate a private mockup url

d. This will lead you to the private mockup page where you can check the 
associated information (files, samples, funding, links, ...)

6. Check files urls, by running the file checker on the bastion server
```
$ cd /home/centos/gigadb-website-develop
$ docker-compose run --rm  test ./protected/yiic files checkUrls --doi=(insert doi here)
```

If the script has any output, send it to the curators for them to check the 
paths.

7. Update the MD5 checksum file attribute values for all files in the dataset:
```
$ docker-compose run --rm  test ./protected/yiic files updateMD5FileAttributes --doi=<insert doi here>
```