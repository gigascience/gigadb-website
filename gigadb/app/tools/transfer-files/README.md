# A tool for transferring files to wasabi and aws s3 bucket


Here is the script usage output:

```
% scripts/transfer.sh 
Usage: scripts/transfer.sh --doi <DOI> --sourcePath <Source Path>

Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
scripts/transfer.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
scripts/transfer.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
scripts/transfer.sh --doi 100148 --sourcePath /share/dropbox/user101 --backup
scripts/transfer.sh --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
scripts/transfer.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
scripts/transfer.sh --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply

```

User has to specify the `--doi` which defines the destination folder name, and the `--sourcePath` defines which file or directory to be copied over.

`transfer.sh` is a script that can execute 2 rclone copy actions:

1. `--wasabi` option will copy files from local to wasabi bucket
2. `--backup` option will copy files from local s3 bucket in glacier tier
3. These two options can be supplied separately or together 

`transfer.sh` would execute the copy actions in dry-run mode by default, unless option `--apply` is supplied.

And the log output of the `transfer.sh` would be saved to `/gigadb/app/tools/transfer-files/log` dir or in `/var/log/gigadb/` in production environments.
The format of the log output file name is in `transfer.log`

Define the profile name [aws-transfer] and [wasabi-transfer] in the /home/$user/~.aws/credentials, 
by adding parameter [s3-profile](https://rclone.org/s3/#s3-profile) to the rclone cmd, the rclone will then able to get the corresponding secrets from /home/$user/~.aws/credentials

### Pre-requisite
1. rclone is installed in dev, eg:
```
 % rclone --version
rclone v1.67.0
- os/version: darwin 13.4.1 (64 bit)
- os/kernel: 22.5.0 (arm64)
- os/type: darwin
- os/arch: arm64 (ARMv8 compatible)
- go/version: go1.22.4
- go/linking: dynamic
- go/tags: none
```
2. The following variables need to be added into your gitlab project variable page

| key | value | env     |
| --- | --- |---------|
| WASABI_ACCESS_KEY_ID | $developer_wasabi_access_key | dev     |
| WASABI_SECRET_ACCESS_KEY | $developer_wasabi_secret_access_key | dev     |
| WASABI_ACCESS_KEY_ID | $developer_wasabi_access_key | staging |
| WASABI_SECRET_ACCESS_KEY | $developer_wasabi_secret_access_key | staging |
| WASABI_ACCESS_KEY_ID | $developer_wasabi_access_key | live    |
| WASABI_SECRET_ACCESS_KEY | $developer_wasabi_secret_access_key | live    |
| WASABI_DATASETFILES_DIR | wasabi:gigadb-datasets/dev/pub/10.5524 | dev     |
| WASABI_DATASETFILES_DIR | wasabi:gigadb-datasets/staging/pub/10.5524 | staging |
| WASABI_DATASETFILES_DIR | wasabi:gigadb-datasets/live/pub/10.5524 | live    |
| S3_DATASETFILES_DIR| gigadb-datasetfiles:gigadb-datasetfiles-backup/dev/pub/10.5524 | dev     |
| S3_DATASETFILES_DIR| gigadb-datasetfiles:gigadb-datasetfiles-backup/staging/pub/10.5524 | staging |
| S3_DATASETFILES_DIR| gigadb-datasetfiles:gigadb-datasetfiles-backup/live/pub/10.5524 | live    |

3. Check that these variables have been added to gitlab `cnhk-infra` variable page

| key | value | env |
| --- | --- | --- |
| gigadb_datasetfiles_aws_access_key_id | xxxxxxxxxxxxxxxxxx | All (default) |
| gigadb_datasetfiles_aws_secret_access_key | yyyyyyyyyyyyyyyyy | All (default) |


### In dev
```
# update the rclone config file
% cd gigadb/app/tools/transfer-files
# execute the configure script to create the .env file
% ./configure
# in the new `.env` file, uncomment and provide a value for the `GITLAB_PRIVATE_TOKEN` variable and also fill in the REPO_NAME
# then execute the configure script again to create the .secrets file
% ./configure
# Make sure the following credentials have been added into developer's `~/.aws/credentials`, the values from the `.secrets` file
% vi ~/.aws/credentials
[wasabi-transfer]
aws_access_key_id = $WASABI_ACCESS_KEY_ID
aws_secret_access_key = $WASABI_SECRET_ACCESS_KEY

[aws-transfer]
aws_access_key_id = $gigadb_datasetfiles_aws_access_key_id
aws_secret_access_key = $gigadb_datasetfiles_aws_secret_access_key

# execute the bats tests
% bats tests/bats/transfer.bats
transfer.bats
 ✓ No DOI provided
 ✓ Show error and usage if no flag
 ✓ Input DOI out of range
 ✓ Copy files from dev to Wasabi in dry run mode
 ✓ Copy files from dev to Wasabi with apply flag
 ✓ Copy files from dev to s3 in dry run mode
 ✓ Copy files from dev to s3 with apply flag
 ✓ Copy files from dev to Wasabi and s3 in dry run mode
 ✓ Copy files from dev to Wasabi and s3 and apply flag

9 tests, 0 failures

```

### Pre-requisite for using the tool in productions
1. The ansible [posix module](https://docs.ansible.com/ansible/latest/collections/ansible/posix/mount_module.html) is needed for mounting access points in the production servers, it will be installed by
```
% cd ops/infrastructure/envs/environment
$ ansible-galaxy install -r ../../../infrastructure/requirements.yml
```
Where ``environment`` is replaced by ``staging`` or ``live``

Or, it can be installed/updated separately as below:
```
% ansible-galaxy collection install ansible.posix
```
2. The production servers have been spun up by following the [SETUP_PROVISIONING.md](../../../../docs/SETUP_PROVISIONING.md)

### As a centos user in staging
```
% ssh -i path/to/staging/pem centos@$staging-bastion-ip
Activate the web console with: systemctl enable --now cockpit.socket

Last login: Tue Jul  9 05:35:15 2024 from 3.36.204.163
# Confirm access points are mounted 
[centos@ip-10-99-0-151 ~]$ df -hT
Filesystem     Type      Size  Used Avail Use% Mounted on
devtmpfs       devtmpfs  339M     0  339M   0% /dev
tmpfs          tmpfs     372M     0  372M   0% /dev/shm
tmpfs          tmpfs     372M  9.9M  362M   3% /run
tmpfs          tmpfs     372M     0  372M   0% /sys/fs/cgroup
/dev/nvme0n1p1 xfs        30G  5.3G   25G  18% /
tmpfs          tmpfs      75M     0   75M   0% /run/user/1000
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/dropbox
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/config
tmpfs          tmpfs      75M     0   75M   0% /run/user/1001
[centos@ip-10-99-0-151 ~]$ ls -al /share/
$ ls -al /share/
total 8
drwxr-xr-x.  4 centos centos   35 Jul 29 03:37 .
dr-xr-xr-x. 18 root   root    237 Jul 29 03:37 ..
drwxr-xr-x.  2 centos centos 6144 Jul 30 04:01 config
drwxr-xr-x.  2 centos centos 6144 Jul 30 04:01 dropbox

[centos@ip-10-99-0-240 ~]$ 
[centos@ip-10-99-0-212 ~]$ cat files-env 
GIGADB_ENV=staging
WASABI_DATASETFILES_DIR=wasabi:gigadb-datasets/staging/pub/10.5524
S3_DATASETFILES_DIR=gigadb-datasetfiles:gigadb-datasetfiles-backup/staging/pub/10.5524
[centos@ip-10-99-0-212 ~]$ ls /share/dropbox/user101/
analysis_data  readme_102480.txt
[centos@ip-10-99-0-212 ~]$ ls /share/dropbox/user101/analysis_data/
Tree_file.txt
[centos@ip-10-99-0-212 ~]$ /usr/local/bin/transfer
Usage: /usr/local/bin/transfer --doi <DOI> --sourcePath <Source Path>

Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply
[centos@ip-10-99-0-212 ~]$ /usr/local/bin/transfer --doi 102480 --sourcePath /share/dropbox/user101/
Error: please specify --wasabi or --backup or both
Usage: /usr/local/bin/transfer --doi <DOI> --sourcePath <Source Path>

Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply
[centos@ip-10-99-0-212 ~]$ 
[centos@ip-10-99-0-212 ~]$ /usr/local/bin/transfer --doi 102480 --sourcePath /share/dropbox/user101/ --wasabi --backup
More details about copying files to Wasabi bucket, please refer to: /var/log/gigadb/transfer.log
More details about copying files to s3 bucket, please refer to: /var/log/gigadb/transfer.log
[centos@ip-10-99-0-212 ~]$ ls /var/log/gigadb/
transfer.log
[centos@ip-10-99-0-212 ~]$ cat /var/log/gigadb/transfer.log 
2024/09/16 04:23:08 INFO  : Start copying files from staging to Wasabi
2024/09/16 04:23:09 NOTICE: readme_102480.txt: Skipped update modification time as --dry-run is set (size 3.127Ki)
2024/09/16 04:23:09 NOTICE: analysis_data/Tree_file.txt: Skipped update modification time as --dry-run is set (size 359)
2024/09/16 04:23:09 INFO  : There was nothing to transfer
2024/09/16 04:23:09 NOTICE: 
Transferred:              0 B / 0 B, -, 0 B/s, ETA -
Checks:                 2 / 2, 100%
Elapsed time:         0.5s

2024/09/16 04:23:09 INFO  : Executed: rclone copy --s3-no-check-bucket --s3-profile wasabi-transfer /share/dropbox/user101/ wasabi:gigadb-datasets/staging/pub/10.5524/102001_103000/102480 --dry-run --log-file /var/log/gigadb/transfer.log --log-level INFO --stats-log-level DEBUG >> /var/log/gigadb/transfer.log
2024/09/16 04:23:09 INFO  : Successfully copied files to Wasabi bucket for DOI: 102480
2024/09/16 04:23:09 INFO  : Start copying files from staging to s3
2024/09/16 04:23:09 NOTICE: readme_102480.txt: Skipped update modification time as --dry-run is set (size 3.127Ki)
2024/09/16 04:23:09 NOTICE: analysis_data/Tree_file.txt: Skipped update modification time as --dry-run is set (size 359)
2024/09/16 04:23:09 INFO  : There was nothing to transfer
2024/09/16 04:23:09 NOTICE: 
Transferred:              0 B / 0 B, -, 0 B/s, ETA -
Checks:                 2 / 2, 100%
Elapsed time:         0.3s

2024/09/16 04:23:09 INFO  : Executed: rclone copy --s3-no-check-bucket --s3-profile aws-transfer /share/dropbox/user101/ gigadb-datasetfiles:gigadb-datasetfiles-backup/staging/pub/10.5524/102001_103000/102480 --dry-run --log-file /var/log/gigadb/transfer.log --log-level INFO --stats-log-level DEBUG >> /var/log/gigadb/transfer.log
2024/09/16 04:23:09 INFO  : Successfully copied files to s3 bucket for DOI: 102480

[centos@ip-10-99-0-212 ~]$ 
```

### As a user lily in staging
```
% cd ops/infrastructure/envs/staging
% ansible-playbook -i ../../inventories users_playbook.yml -e "newuser=lily" -e "credentials_csv_path=~/path/to/credentials.csv" -e "gigadb_env=staging" 
% chmod 500 output/privkeys-$bastion-ip/lily
% ssh -i output/privkeys-3.36.204.163/lily lily@$bastion-ip
Activate the web console with: systemctl enable --now cockpit.socket

[lily@ip-10-99-0-212 ~]$ ls
files-env  uploadDir
# confirm the files-env contains staging variables
[lily@ip-10-99-0-212 ~]$ cat files-env 
GIGADB_ENV=staging
WASABI_DATASETFILES_DIR=wasabi:gigadb-datasets/staging/pub/10.5524
S3_DATASETFILES_DIR=gigadb-datasetfiles:gigadb-datasetfiles-backup/staging/pub/10.5524
[lily@ip-10-99-0-212 ~]$ ls /share/dropbox/user101/
analysis_data  readme_102480.txt
[lily@ip-10-99-0-212 ~]$ ls /share/dropbox/user101/analysis_data/
Tree_file.txt
[lily@ip-10-99-0-212 ~]$ /usr/local/bin/transfer
Usage: /usr/local/bin/transfer --doi <DOI> --sourcePath <Source Path>

Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply
[lily@ip-10-99-0-212 ~]$ /usr/local/bin/transfer --doi 102481 --sourcePath /share/dropbox/user101/
Error: please specify --wasabi or --backup or both
Usage: /usr/local/bin/transfer --doi <DOI> --sourcePath <Source Path>

Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply
[lily@ip-10-99-0-212 ~]$ /usr/local/bin/transfer --doi 102481 --sourcePath /share/dropbox/user101/ --wasabi
[lily@ip-10-99-0-212 ~]$ ls /var/log/gigadb/
transfer_20240805_041137.log
[lily@ip-10-99-0-212 ~]$ /usr/local/bin/transfer --doi 102481 --sourcePath /share/dropbox/user101/ --wasabi --backup
[lily@ip-10-99-0-212 ~]$ ls /var/log/gigadb/
transfer_20240805_041137.log  transfer_20240805_041332.log
[lily@ip-10-99-0-212 ~]$ cat /var/log/gigadb/transfer_20240805_041332.log 
2024/08/05 04:13:32 INFO  : Start copying files from staging to Wasabi
2024/08/05 04:13:33 NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)
2024/08/05 04:13:33 NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)
2024/08/05 04:13:33 NOTICE: 
Transferred:        3.478 KiB / 3.478 KiB, 100%, 0 B/s, ETA -
Transferred:            2 / 2, 100%
Elapsed time:         0.3s

2024/08/05 04:13:33 INFO  : Executed: rclone copy --s3-no-check-bucket --s3-profile wasabi-transfer /share/dropbox/user101/ wasabi:gigadb-datasets/staging/pub/10.5524/102001_103000/102481 --dry-run --log-file /var/log/gigadb/transfer_20240805_041332.log --log-level INFO --stats-log-level DEBUG >> /var/log/gigadb/transfer_20240805_041332.log
2024/08/05 04:13:33 INFO  : Successfully copied files to Wasabi bucket for DOI: 102481
2024/08/05 04:13:33 INFO  : Start copying files from staging to s3
2024/08/05 04:13:33 NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)
2024/08/05 04:13:33 NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)
2024/08/05 04:13:33 NOTICE: 
Transferred:        3.478 KiB / 3.478 KiB, 100%, 0 B/s, ETA -
Transferred:            2 / 2, 100%
Elapsed time:         0.2s

2024/08/05 04:13:33 INFO  : Executed: rclone copy --s3-no-check-bucket --s3-profile aws-transfer /share/dropbox/user101/ gigadb-datasetfiles:gigadb-datasetfiles-backup/staging/pub/10.5524/102001_103000/102481 --dry-run --log-file /var/log/gigadb/transfer_20240805_041332.log --log-level INFO --stats-log-level DEBUG >> /var/log/gigadb/transfer_20240805_041332.log
2024/08/05 04:13:33 INFO  : Successfully copied files to s3 bucket for DOI: 102481
[lily@ip-10-99-0-212 ~]$ 

```

### As a centos user in live
```
% ssh -i path/to/live/pem centos@$live-bastion-ip
Activate the web console with: systemctl enable --now cockpit.socket

Last login: Mon Aug  5 04:48:09 2024 from 54.180.33.208
[centos@ip-10-99-0-253 ~]$ df -hT
Filesystem     Type      Size  Used Avail Use% Mounted on
devtmpfs       devtmpfs  339M     0  339M   0% /dev
tmpfs          tmpfs     372M     0  372M   0% /dev/shm
tmpfs          tmpfs     372M  5.3M  367M   2% /run
tmpfs          tmpfs     372M     0  372M   0% /sys/fs/cgroup
/dev/nvme0n1p1 xfs        30G  2.5G   28G   9% /
tmpfs          tmpfs      75M     0   75M   0% /run/user/1000
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/dropbox
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/config
[centos@ip-10-99-0-253 ~]$ cat files-env 
GIGADB_ENV=live
WASABI_DATASETFILES_DIR=wasabi:gigadb-datasets/live/pub/10.5524
S3_DATASETFILES_DIR=gigadb-datasetfiles:gigadb-datasetfiles-backup/live/pub/10.5524
[centos@ip-10-99-0-253 ~]$ ls /share/dropbox/user101/
analysis_data  readme_102480.txt
[centos@ip-10-99-0-253 ~]$ ls /share/dropbox/user101/analysis_data/
Tree_file.txt
[centos@ip-10-99-0-253 ~]$ /usr/local/bin/transfer
Usage: /usr/local/bin/transfer --doi <DOI> --sourcePath <Source Path>

Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply
[centos@ip-10-99-0-253 ~]$ /usr/local/bin/transfer --doi 102480 --sourcePath /share/dropbox/user101/
Error: please specify --wasabi or --backup or both
Usage: /usr/local/bin/transfer --doi <DOI> --sourcePath <Source Path>

Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply
[centos@ip-10-99-0-253 ~]$ ls /var/log/gigadb/
[centos@ip-10-99-0-253 ~]$ /usr/local/bin/transfer --doi 102480 --sourcePath /share/dropbox/user101/ --wasabi --backup
[centos@ip-10-99-0-253 ~]$ ls /var/log/gigadb/
transfer_20240805_054904.log
[centos@ip-10-99-0-253 ~]$ cat /var/log/gigadb/transfer_20240805_054904.log 
2024/08/05 05:49:04 INFO  : Start copying files from live to Wasabi
2024/08/05 05:49:05 NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)
2024/08/05 05:49:05 NOTICE: analysis_data/Tree_file.txt: Skipped update modification time as --dry-run is set (size 359)
2024/08/05 05:49:05 NOTICE: 
Transferred:        3.127 KiB / 3.127 KiB, 100%, 0 B/s, ETA -
Checks:                 2 / 2, 100%
Transferred:            1 / 1, 100%
Elapsed time:         1.5s

2024/08/05 05:49:05 INFO  : Executed: rclone copy --s3-no-check-bucket --s3-profile wasabi-transfer /share/dropbox/user101/ wasabi:gigadb-datasets/live/pub/10.5524/102001_103000/102480 --dry-run --log-file /var/log/gigadb/transfer_20240805_054904.log --log-level INFO --stats-log-level DEBUG >> /var/log/gigadb/transfer_20240805_054904.log
2024/08/05 05:49:05 INFO  : Successfully copied files to Wasabi bucket for DOI: 102480
2024/08/05 05:49:05 INFO  : Start copying files from live to s3
2024/08/05 05:49:06 NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)
2024/08/05 05:49:06 NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)
2024/08/05 05:49:06 NOTICE: 
Transferred:        3.478 KiB / 3.478 KiB, 100%, 0 B/s, ETA -
Transferred:            2 / 2, 100%
Elapsed time:         0.2s

2024/08/05 05:49:06 INFO  : Executed: rclone copy --s3-no-check-bucket --s3-profile aws-transfer/share/dropbox/user101/ gigadb-datasetfiles:gigadb-datasetfiles-backup/live/pub/10.5524/102001_103000/102480 --dry-run --log-file /var/log/gigadb/transfer_20240805_054904.log --log-level INFO --stats-log-level DEBUG >> /var/log/gigadb/transfer_20240805_054904.log
2024/08/05 05:49:06 INFO  : Successfully copied files to s3 bucket for DOI: 102480
[centos@ip-10-99-0-253 ~]$ 
```

### As a user lily in live
```
% cd ops/infrastructure/envs/staging
% ansible-playbook -i ../../inventories users_playbook.yml -e "newuser=lily" -e "credentials_csv_path=~/Downloads/credentials.csv" -e "gigadb_env=live"
% chmod 500 output/privkeys-$bastion-ip/lily
% ssh -i utput/privkeys-54.180.33.208/lily lily@$bastion-ip
[lily@ip-10-99-0-253 ~]$ ls
files-env  uploadDir
[lily@ip-10-99-0-253 ~]$ df -hT
Filesystem     Type      Size  Used Avail Use% Mounted on
devtmpfs       devtmpfs  339M     0  339M   0% /dev
tmpfs          tmpfs     372M     0  372M   0% /dev/shm
tmpfs          tmpfs     372M  9.9M  362M   3% /run
tmpfs          tmpfs     372M     0  372M   0% /sys/fs/cgroup
/dev/nvme0n1p1 xfs        30G  2.5G   28G   9% /
tmpfs          tmpfs      75M     0   75M   0% /run/user/1000
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/dropbox
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/config
tmpfs          tmpfs      75M     0   75M   0% /run/user/1001
[lily@ip-10-99-0-253 ~]$ cat files-env 
GIGADB_ENV=live
WASABI_DATASETFILES_DIR=wasabi:gigadb-datasets/live/pub/10.5524
S3_DATASETFILES_DIR=gigadb-datasetfiles:gigadb-datasetfiles-backup/live/pub/10.5524
[lily@ip-10-99-0-253 ~]$ ls /share/dropbox/user101/
analysis_data  readme_102480.txt
[lily@ip-10-99-0-253 ~]$ ls /share/dropbox/user101/analysis_data/
Tree_file.txt
[lily@ip-10-99-0-253 ~]$ ls /var/log/gigadb/
transfer_20240805_054904.log
[lily@ip-10-99-0-253 ~]$ /usr/local/bin/transfer
Usage: /usr/local/bin/transfer --doi <DOI> --sourcePath <Source Path>

Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply
[lily@ip-10-99-0-253 ~]$ /usr/local/bin/transfer --doi 102480 --sourcePath /share/dropbox/user101/
Error: please specify --wasabi or --backup or both
Usage: /usr/local/bin/transfer --doi <DOI> --sourcePath <Source Path>

Required:
--doi            Specify DOI to process
--sourcePath     Specify source path
--wasabi         Copy files to Wasabi bucket
--backup         Copy files to s3 bucket

Available Option:
--apply          Escape dry run mode

Example usages:
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --backup --apply
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup
/usr/local/bin/transfer --doi 100148 --sourcePath /share/dropbox/user101 --wasabi --backup --apply
[lily@ip-10-99-0-253 ~]$ ls /var/log/gigadb/
transfer_20240805_054904.log  transfer_20240805_061241.log
[lily@ip-10-99-0-253 ~]$ cat /var/log/gigadb/transfer_20240805_061241.log 
2024/08/05 06:12:41 INFO  : Start copying files from live to Wasabi
2024/08/05 06:12:43 NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)
2024/08/05 06:12:43 NOTICE: analysis_data/Tree_file.txt: Skipped update modification time as --dry-run is set (size 359)
2024/08/05 06:12:43 NOTICE: 
Transferred:        3.127 KiB / 3.127 KiB, 100%, 0 B/s, ETA -
Checks:                 2 / 2, 100%
Transferred:            1 / 1, 100%
Elapsed time:         1.4s

2024/08/05 06:12:43 INFO  : Executed: rclone copy --s3-no-check-bucket --s3-profile wasabi-transfer /share/dropbox/user101/ wasabi:gigadb-datasets/live/pub/10.5524/102001_103000/102480 --dry-run --log-file /var/log/gigadb/transfer_20240805_061241.log --log-level INFO --stats-log-level DEBUG >> /var/log/gigadb/transfer_20240805_061241.log
2024/08/05 06:12:43 INFO  : Successfully copied files to Wasabi bucket for DOI: 102480
2024/08/05 06:12:43 INFO  : Start copying files from live to s3
2024/08/05 06:12:43 NOTICE: readme_102480.txt: Skipped copy as --dry-run is set (size 3.127Ki)
2024/08/05 06:12:43 NOTICE: analysis_data/Tree_file.txt: Skipped copy as --dry-run is set (size 359)
2024/08/05 06:12:43 NOTICE: 
Transferred:        3.478 KiB / 3.478 KiB, 100%, 0 B/s, ETA -
Transferred:            2 / 2, 100%
Elapsed time:         0.2s

2024/08/05 06:12:43 INFO  : Executed: rclone copy --s3-no-check-bucket --s3-profile aws-transfer /share/dropbox/user101/ gigadb-datasetfiles:gigadb-datasetfiles-backup/live/pub/10.5524/102001_103000/102480 --dry-run --log-file /var/log/gigadb/transfer_20240805_061241.log --log-level INFO --stats-log-level DEBUG >> /var/log/gigadb/transfer_20240805_061241.log
2024/08/05 06:12:43 INFO  : Successfully copied files to s3 bucket for DOI: 102480
```
