# Migrate Gigadb data from CNGB to Wasabi cloud storage

## Introduction
A tool `swatchdog` will continuously look for the specific pattern found in the rclone output log, and then it will trigger the `send_notification.sh` which will then push that particularly matched log line to gitter `GigaScience-IT-Notification` channel.

The `generate_rclone_config.sh` script will generate `rclone.conf` for connecting different buckets  and `env` for pushing message to gitter chat room.
Appending `--config=` option to rclone makes changing the bucket endpoints easily, and it provides flexibility to config multiple endpoints by just editing the `rclone.conf`.

For testing, the main tools `rclone` and ` swatch` are containerized in image `gigadb-alpine`, this allows a consistent and stable  environment for running tools.

## How to use in dev environment
```
% cd gigadb/app/tools/gigadb-cngb-backup-tool
% ./generate_rclone_config.sh
% docker image build -q -t gigadb-alpine .
% docker run --rm -it gigadb-alpine       
/gigadb-cngb-backup-tool # ls
Dockerfile                 config-source              logs                       tests
README.md                  generate_rclone_config.sh  scripts

# Test the notification functionality locally
/gigadb-cngb-backup-tool # swatchdog --version
This is swatchdog version 3.2.4
Built on Aug 25, 2008
Built by E. Todd Atkins <Todd.Atkins@StanfordAlumni.ORG>
/gigadb-cngb-backup-tool # ps
PID   USER     TIME  COMMAND
    1 root      0:00 /bin/sh
   37 root      0:00 ps
/gigadb-cngb-backup-tool # swatchdog -c swatchdog/swatchdog.conf -t tests/test-logs/test_rclone_copy_large_success.log --daemon
/gigadb-cngb-backup-tool # ps
PID   USER     TIME  COMMAND
    1 root      0:00 /bin/sh
   42 root      0:00 {/usr/bin/swatch} /usr/bin/swatchdog -c swatchdog/swatchdog.conf -t tests/test-logs/test_rclone_copy_large_success.log --daemon
   43 root      0:00 /usr/bin/tail -n 0 -F tests/test-logs/test_rclone_copy_large_success.log
   44 root      0:00 ps
/gigadb-cngb-backup-tool # echo "2022/10/26 12:03:44 INFO  : Hello World From Docker" >> tests/test-logs/test_rclone_copy_large_success.log
/gigadb-cngb-backup-tool # 2022/10/26 12:03:44 INFO  : Hello World From Docker
# Then check the message in the gitter notification room

# Test the rclone copy functionality locally
/gigadb-cngb-backup-tool # rclone --version
rclone v1.60.0
- os/version: alpine 3.16.2 (64 bit)
- os/kernel: 5.10.47-linuxkit (x86_64)
- os/type: linux
- os/arch: amd64
- go/version: go1.19.2
- go/linking: static
- go/tags: none
/gigadb-cngb-backup-tool # rclone --config=.rclone.conf lsd wasabi:test-gigadb-datasets
           0 2022-11-01 08:04:54        -1 peter
           0 2022-11-01 08:04:54        -1 test-ken-20221010
/gigadb-cngb-backup-tool # rclone --config=.rclone.conf ls s3genomics:1000genomes/data/NA12878/alignment/
      621 NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.bam.bas
18307938102 NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.cram
   391987 NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.cram.crai
/gigadb-cngb-backup-tool # swatchdog -c swatchdog/swatchdog.conf -t logs/rclone_copy_change_policy.log --daemon
/gigadb-cngb-backup-tool # rclone --config=.rclone.conf copy --checksum --log-level DEBUG --log-file=logs/rclone_copy_change_policy.log s3genomics:1000genomes/data/NA12878/alignment/NA12878.alt_
bwamem_GRCh38DH.20150718.CEU.low_coverage.bam.bas wasabi:test-gigadb-datasets/test-ken-20221010/folder_to_delete
2022/11/01 08:32:36 ERROR : NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.bam.bas: Failed to copy: AccessDenied: User: arn:aws:iam::100000166496:user/Ken is not authorized to perform: s3:CreateBucket on resource: arn:aws:s3:::test-gigadb-datasets
2022/11/01 08:32:36 ERROR : Attempt 1/3 failed with 1 errors and: AccessDenied: User: arn:aws:iam::100000166496:user/Ken is not authorized to perform: s3:CreateBucket on resource: arn:aws:s3:::test-gigadb-datasets
2022/11/01 08:32:38 ERROR : NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.bam.bas: Failed to copy: AccessDenied: User: arn:aws:iam::100000166496:user/Ken is not authorized to perform: s3:CreateBucket on resource: arn:aws:s3:::test-gigadb-datasets
2022/11/01 08:32:38 ERROR : Attempt 2/3 failed with 1 errors and: AccessDenied: User: arn:aws:iam::100000166496:user/Ken is not authorized to perform: s3:CreateBucket on resource: arn:aws:s3:::test-gigadb-datasets
/gigadb-cngb-backup-tool # rclone --config=.rclone.conf copy --checksum --log-level DEBUG --s3-no-check-bucket --log-file=logs/rclone_copy_change_policy.log s3genomics:1000genomes/data/NA12878/a
lignment/NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.bam.bas wasabi:test-gigadb-datasets/test-ken-20221010/folder_to_delete
/gigadb-cngb-backup-tool # 2022/11/01 08:35:37 INFO  : NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.bam.bas: Copied (new)
# Then check the message in the gitter notification room

# Start large file transfer process, about 17Gb in size
/gigadb-cngb-backup-tool # rclone --config=.rclone.conf copy --checksum --log-level DEBUG --s3-no-check-bucket --log-file=logs/rclone_copy_change_policy.log s3genomics:1000genomes/data/NA12878/a
lignment/ wasabi:test-gigadb-datasets/test-ken-20221010/folder_to_delete

# Then make the policy v9 (bucket test-gigadb-datasets is excluded) as the default
2022/11/01 08:50:12 ERROR : NA12878.alt_bwamem_GRCh38DH.20150718.CEU.low_coverage.cram: Failed to copy: multipart upload failed to initialise: AccessDenied: Access Denied
2022/11/01 08:50:12 ERROR : Attempt 1/3 failed with 1 errors and: multipart upload failed to initialise: AccessDenied: Access Denied
2022/11/01 08:50:13 ERROR : S3 bucket test-gigadb-datasets path test-ken-20221010/folder_to_delete: error reading destination root directory: AccessDenied: Access Denied
2022/11/01 08:50:13 ERROR : Attempt 2/3 failed with 1 errors and: AccessDenied: Access Denied
2022/11/01 08:50:14 ERROR : S3 bucket test-gigadb-datasets path test-ken-20221010/folder_to_delete: error reading destination root directory: AccessDenied: Access Denied
2022/11/01 08:50:14 ERROR : Attempt 3/3 failed with 1 errors and: AccessDenied: Access Denied
# Then check the message in the gitter notification room
```

## How to use in live environment
```
# Test the notification functionality in cngb gigadb bak server
# Prerequisite:
 - Connect to BGI vpn
 - Log in smoc.genomics.cn
 - Log in `cngb-gigadb-bak`  with server ip `10.50.11.48` as user `gigadb`
 - Tool `swatch` is installed
 - .env file with `GITTER_API_TOKEN` and `GITTER_IT_NOTIFICATION_ROOM_ID`

[gigadb@cngb-gigadb-bak ken]$ cat /etc/*-release
CentOS Linux release 7.5.1804 (Core)
...
[gigadb@cngb-gigadb-bak ~]$ swatch --version
This is swatch version 3.2.3
Built on May 7, 2008
Built by E. Todd Atkins <Todd.Atkins@StanfordAlumni.ORG>
gigadb@cngb-gigadb-bak ~]$ cd ken
[gigadb@cngb-gigadb-bak ken]$
[gigadb@cngb-gigadb-bak ken]$ ls -a
.env README.md  cronjob.txt  logs  send_notification.sh  swatchdog  test_scripts
[gigadb@cngb-gigadb-bak ken]$ ls swatchdog/
swatchdog.conf
[gigadb@cngb-gigadb-bak ken]$ ls logs/
test-swatchdog.log
[gigadb@cngb-gigadb-bak ken]$ 
[gigadb@cngb-gigadb-bak ken]$ swatch -c swatchdog/swatchdog.conf -t logs/test-swatchdog.log --daemon
[gigadb@cngb-gigadb-bak ken]$ echo "2022/11/01 14:02:44 INFO  : Test swatch tool is working" >> logs/test-swatchdog.log
[gigadb@cngb-gigadb-bak ken]$ 2022/11/01 14:02:44 INFO  : Test swatch tool is working 
# Then check the message in the gitter notification room
[gigadb@cngb-gigadb-bak ken]$ ps -ef | grep swatch
gigadb   17931     1  0 14:02 ?        00:00:00 /usr/bin/swatch -c swatchdog/swatchdog.conf -t logs/test-swatchdog.log --daemon
gigadb   17932 17931  0 14:02 ?        00:00:00 /usr/bin/tail -n 0 -F logs/test-swatchdog.log
gigadb   22050 21964  0 14:20 pts/2    00:00:00 grep --color=auto swatch
```

## Different rclone scenarios with its log output
1. `test_rclone_copy_success.log` contains the logs of successful transfer.
2. `test_rclone_error_access_denied.log` contains the logs of  simulating `User: is not authorized to perform: s3:CreateBucket`, but can be overridden by `"--s3-no-check-bucket"`.
3. `test_rclone_copy_large_success.log` and `test_rclone_copy_large_success_2nd.log` contain logs of transferring large file and re-transferring the same large file, the re-transfer process finished very fast since `Size of src and dst objects identical`.
4. `test_rclone_copy_large_interrupt_fail.log` contains the logs of manually stop the rclone process by `crtl C`.
5.  `test_rclone_copy_interrupt_network.log` contains the logs of turning off the internet connection during transfer process.
6. `test_rclone_copy_large_change_file_name.log` contains the logs of changing the source file name during transfer process, the process would keep going on.
7. `rclone_copy_change_policy.log` contains the logs of changing the bucket policy during transfer process, the process would stop because `AccessDenied: Access Denied` .


### Notes
1. `swatchdog` is now official name for `swatch` according to [here](https://sourceforge.net/projects/swatch/).