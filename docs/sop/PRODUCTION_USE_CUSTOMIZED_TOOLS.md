# SOP: Using customized tools in the production bastion server


### Confirm the customized tools are available
```
# Given a user has logged in the bastion server, in this case is lily
[lily@ip-10-99-0-88 ~]$ ls
uploadDir
[lily@ip-10-99-0-88 ~]$ ls /usr/local/bin/
__pycache__  calculateChecksumSizes  createReadme  datasetUpload  docker-compose  node_exporter  postUpload  rclone  updateUrls  wsdump.py
[lily@ip-10-99-0-88 ~]$ 
[lily@ip-10-99-0-88 ~]$ rclone version
rclone v1.60.0
- os/version: centos 8 (64 bit)
- os/kernel: 4.18.0-545.el8.x86_64 (x86_64)
- os/type: linux
- os/arch: amd64
- go/version: go1.19.2
- go/linking: static
- go/tags: none
```

### How to calculate the md5 values and the file sizes of the files inside a user dropbox
```
# confirm the aws efs dropbox is mounted
[lily@ip-10-99-0-88 ~]$ df -hT
Filesystem     Type      Size  Used Avail Use% Mounted on
devtmpfs       devtmpfs  838M     0  838M   0% /dev
tmpfs          tmpfs     871M     0  871M   0% /dev/shm
tmpfs          tmpfs     871M   17M  854M   2% /run
tmpfs          tmpfs     871M     0  871M   0% /sys/fs/cgroup
/dev/nvme0n1p1 xfs        30G  3.8G   27G  13% /
tmpfs          tmpfs     175M     0  175M   0% /run/user/1000
tmpfs          tmpfs     175M     0  175M   0% /run/user/1001
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/config
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/dropbox
# go to a dropbox where you want to do the calculation, eg. user103
[lily@ip-10-99-0-88 ~]$ cd /share/dropbox/user103/
# execute the script without DOI input
[lily@ip-10-99-0-88 user103]$ /usr/local/bin/calculateChecksumSizes
Usage: /usr/local/bin/calculateChecksumSizes <DOI>
Calculates and uploads MD5 checksums values and file sizes for the given DOI to the aws s3 bucket - gigadb-datasets-metadata.
# execute the script with DOI, which is assigned by the user
[lily@ip-10-99-0-88 user103]$ /usr/local/bin/calculateChecksumSizes 111111
Created 111111.md5
Created 111111.filesizes
2024/05/07 14:14:46 INFO  : 111111.filesizes: Copied (new)
2024/05/07 14:14:46 INFO  : 
Transferred:            106 B / 106 B, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.3s

2024/05/07 14:14:47 INFO  : 111111.md5: Copied (new)
2024/05/07 14:14:47 INFO  : 
Transferred:            287 B / 287 B, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.3s
# check the $doi.filesize and $doi.md5 files are created in /share/dropbox/user103
[lily@ip-10-99-0-88 user103]$ ls
111111.filesizes  111111.md5  AD25.txt  NO25.txt  readme.txt  user103.md5
# check the $doi.filesizes and $doi.md5 files has been uploaded AWS S3 bucket:gigadb-datasets-metadata
[lily@ip-10-99-0-88 user103]$ rclone ls aws_metadata:gigadb-datasets-metadata
      352 100006.filesizes
      596 100006.md5
      164 100039.filesizes
      137 100039.filesizes.original
      107 100142.filesizes
       68 102480.filesizes
      118 102480.md5
      105 102499.filesizes
      161 102499.md5
     3825 102515.filesizes
     5420 102515.md5
     2704 102519.filesizes
     2330 102519.md5
     2213 102520.filesizes
     3483 102520.md5
     1698 102522.filesizes
     1303 102522.md5
     4622 102523.filesizes
     7371 102523.md5
      764 102524.filesizes
      692 102524.md5
     5141 102526.filesizes
     9251 102526.md5
       65 1111111.filesizes
      185 1111111.md5
# if necessary, user can delete the files on the AWS S3 bucket
[lily@ip-10-99-0-88 user103]$ rclone delete aws_metadata:gigadb-datasets-metadata/1111111.filesizes -v
2024/05/07 08:20:06 INFO  : 1111111.filesizes: Deleted
[lily@ip-10-99-0-88 user103]$ rclone delete aws_metadata:gigadb-datasets-metadata/1111111.md5 -v
2024/05/07 08:25:06 INFO  : 1111111.md5: Deleted
[lily@ip-10-99-0-88 user103]$ 
[lily@ip-10-99-0-88 user103]$ rclone ls aws_metadata:gigadb-datasets-metadata
      352 100006.filesizes
      596 100006.md5
      164 100039.filesizes
      137 100039.filesizes.original
      107 100142.filesizes
       68 102480.filesizes
      118 102480.md5
      105 102499.filesizes
      161 102499.md5
     3825 102515.filesizes
     5420 102515.md5
     2704 102519.filesizes
     2330 102519.md5
     2213 102520.filesizes
     3483 102520.md5
     1698 102522.filesizes
     1303 102522.md5
     4622 102523.filesizes
     7371 102523.md5
      764 102524.filesizes
      692 102524.md5
     5141 102526.filesizes
     9251 102526.md5
[lily@ip-10-99-0-88 ~]$
```

### Using rclone as a tool to interact different cloud storages

[rclone](https://rclone.org/) is an open source tool that allows user to manage files and directories in multiple cloud storages.

Here is the usage example on using rclone in the bastion server as a user.

##### Confirm what are the available storages rclone can interact
```
# check the available storages that rclone can interact
[lily@ip-10-99-0-88 ~]$ cat .config/rclone/rclone.conf
[aws_metadata]
type = s3
provider = AWS
env_auth = true
shared_credentials_file = /etc/aws/credentials
region = ap-northeast-1
location_constraint = ap-northeast-1
acl = private
no_check_bucket = true

[wasabi_lily]
type = s3
env_auth = true
region = ap-northeast-1
endpoint = s3.ap-northeast-1.wasabisys.com
location_constraint =
acl = public-read
server_side_encryption =
storage_class =
provider = Other
no_check_bucket = true
[lily@ip-10-99-0-88 ~]$
```

##### Basic rclone usages
```
# copy a file to aws s3 bucket:gigadb-datasets-metadatain in dry run mode
[lily@ip-10-99-0-88 ~]$ rclone copy -v test.txt aws_metadata:gigadb-datasets-metadata --dry-run
2024/05/09 11:13:29 NOTICE: test.txt: Skipped copy as --dry-run is set (size 931)
2024/05/09 11:13:29 NOTICE: 
Transferred:            931 B / 931 B, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.3s
# copy a file to aws s3 bucket:gigadb-datasets-metadata
[lily@ip-10-99-0-88 ~]$ rclone copy -v test.txt aws_metadata:gigadb-datasets-metadata
2024/05/09 11:15:52 INFO  : test.txt: Copied (new)
2024/05/09 11:15:52 INFO  : 
Transferred:            931 B / 931 B, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.6s
# list the files
[lily@ip-10-99-0-88 ~]$ rclone ls aws_metadata:gigadb-datasets-metadata
      352 100006.filesizes
      596 100006.md5
      137 100039.filesizes
      107 100142.filesizes
       68 102480.filesizes
      118 102480.md5
      105 102499.filesizes
      161 102499.md5
     3825 102515.filesizes
     5420 102515.md5
     2704 102519.filesizes
     2330 102519.md5
     2213 102520.filesizes
     3483 102520.md5
     1698 102522.filesizes
     1303 102522.md5
     4622 102523.filesizes
     7371 102523.md5
      764 102524.filesizes
      692 102524.md5
     5141 102526.filesizes
     9251 102526.md5
       27 test.txt
# delete the files to aws s3 bucket:gigadb-datasets-metadata in dry run mode
[lily@ip-10-99-0-88 ~]$ rclone delete -v aws_metadata:gigadb-datasets-metadata/test.txt --dry-run
2024/05/09 11:18:20 NOTICE: test.txt: Skipped delete as --dry-run is set (size 27)
# delete the files to aws s3 bucket:gigadb-datasets-metadata
[lily@ip-10-99-0-88 ~]$ rclone delete -v aws_metadata:gigadb-datasets-metadata/test.txt
2024/05/09 11:20:38 INFO  : test.txt: Deleted
[lily@ip-10-99-0-88 ~]$ rclone ls aws_metadata:gigadb-datasets-metadata
      352 100006.filesizes
      596 100006.md5
      137 100039.filesizes
      107 100142.filesizes
       68 102480.filesizes
      118 102480.md5
      105 102499.filesizes
      161 102499.md5
     3825 102515.filesizes
     5420 102515.md5
     2704 102519.filesizes
     2330 102519.md5
     2213 102520.filesizes
     3483 102520.md5
     1698 102522.filesizes
     1303 102522.md5
     4622 102523.filesizes
     7371 102523.md5
      764 102524.filesizes
      692 102524.md5
     5141 102526.filesizes
     9251 102526.md5
# list the bucket directories if it has
[lily@ip-10-99-0-88 ~]$ rclone lsd wasabi_lily:
          -1 2023-10-19 07:00:33        -1 cms-assets
          -1 2022-10-24 08:32:33        -1 gigadb-datasets
          -1 2023-05-22 15:59:31        -1 gigadb-datasets-logs
          -1 2023-06-14 16:34:29        -1 gigadb-monitoring-resources
          -1 2024-04-24 19:13:29        -1 infra-resources
          -1 2022-10-07 07:30:21        -1 test-gigadb-datasets
          -1 2022-11-15 06:32:08        -1 test-sg-bucket
          -1 2022-11-15 06:34:04        -1 test-tyo-bucket
          -1 2022-11-17 07:58:16        -1 test-us-east1-bucket
```