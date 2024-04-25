# SOP: Instructions for migrating data in CNGB server to AWS EFS

### Pre-requisite
1. BGI VPN is connected
2. A bastion user is created and the private key is reachable
3. Valid credentials for accessing the CNGB server
4. AWS EFS has been instantiated

### How to manually migrate user dropboxs in cngb ftp server to aws efs
0. Access to this [spreadsheet](https://docs.google.com/spreadsheets/d/1qvfqRhfkoMCyubfL7z42hQmFLOozVbD2BYBSPBPPbwU/edit#gid=0) and identify which user dropbox is labelled with `COPY TO NEW SERVER` 
1. Log in the BGI server admin page at [here](https://uomc.genomics.cn/shterm/login)
2. Download the AccessClient from the page and install it if not yet installed
3. Click the ssh icon next to the server IP, a command line terminal will be popped up 
4. Log in the server, eg. cngb ftp server, with the valid credentials, and perform the migration steps:
```
login: ken
ken@192.168.28.205's password: 
Last login: Tue Apr 23 22:05:50 2024 from 10.17.24.13

Welcome to Alibaba Cloud Elastic Compute Service !

-bash: warning: setlocale: LC_CTYPE: cannot change locale (UTF-8): No such file or directory
[ken@cngb-gigadb-ftp ~]$ 
[ken@cngb-gigadb-ftp ~]$ df -hT
Filesystem                                                 Type      Size  Used Avail Use% Mounted on
/dev/vda1                                                  ext4       50G   17G   31G  36% /
devtmpfs                                                   devtmpfs   16G     0   16G   0% /dev
tmpfs                                                      tmpfs      16G     0   16G   0% /dev/shm
tmpfs                                                      tmpfs      16G  580K   16G   1% /run
tmpfs                                                      tmpfs      16G     0   16G   0% /sys/fs/cgroup
240704b1c5-eey91.cn-shenzhen.nas.aliyuncs.com:/data_upload nfs        10P  6.9T   10P   1% /data
192.168.56.61:/data/gigadb                                 nfs4      191T  166T   15T  92% /var/ftp/gigadb
tmpfs                                                      tmpfs     3.1G     0  3.1G   0% /run/user/5057
[ken@cngb-gigadb-ftp ~]$ ls /data/data_upload/
user1    user111  user16  user29  user41  user54  user67  user8   user92
user10   user112  user17  user3   user42  user55  user68  user80  user93
user100  user113  user18  user30  user43  user56  user69  user81  user94
user101  user114  user19  user31  user44  user57  user7   user82  user95
user102  user115  user2   user32  user45  user58  user70  user83  user96
user103  user116  user20  user33  user46  user59  user71  user84  user97
user104  user117  user21  user34  user47  user6   user72  user85  user98
user105  user118  user22  user35  user48  user60  user73  user86  user99
user106  user119  user23  user36  user49  user61  user74  user87  user_gaofei
user107  user12   user24  user37  user5   user62  user75  user88
user108  user120  user25  user38  user50  user63  user76  user89
user109  user13   user26  user39  user51  user64  user77  user9
user11   user14   user27  user4   user52  user65  user78  user90
user110  user15   user28  user40  user53  user66  user79  user91
# install go by following https://go.dev/doc/install#requirements if not yet installed
[ken@cngb-gigadb-ftp ~]$ sudo rm -rf /usr/local/go && sudo tar -C /usr/local -xzf go1.22.2.linux-amd64.tar.gz
[ken@cngb-gigadb-ftp ~]$ export PATH=$PATH:/usr/local/go/bin
[ken@cngb-gigadb-ftp ~]$ go version
go version go1.22.2 linux/amd64
# install rclone by following https://rclone.org/install/#linux
[ken@cngb-gigadb-ftp ~]$ curl -O https://downloads.rclone.org/rclone-current-linux-amd64.zip
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100 20.1M  100 20.1M    0     0  2116k      0  0:00:09  0:00:09 --:--:-- 4154k
[ken@cngb-gigadb-ftp ~]$ unzip rclone-current-linux-amd64.zip
[ken@cngb-gigadb-ftp ~]$ cd rclone-*-linux-amd64
[ken@cngb-gigadb-ftp rclone-v1.66.0-linux-amd64]$ sudo cp rclone /usr/bin/
[ken@cngb-gigadb-ftp rclone-v1.66.0-linux-amd64]$ sudo chown root:root /usr/bin/rclone
[ken@cngb-gigadb-ftp rclone-v1.66.0-linux-amd64]$ sudo chmod 755 /usr/bin/rclone
[ken@cngb-gigadb-ftp rclone-v1.66.0-linux-amd64]$ sudo mkdir -p /usr/local/share/man/man1
[ken@cngb-gigadb-ftp rclone-v1.66.0-linux-amd64]$ sudo cp rclone.1 /usr/local/share/man/man1/
[ken@cngb-gigadb-ftp rclone-v1.66.0-linux-amd64]$ sudo mandb
[ken@cngb-gigadb-ftp rclone-v1.66.0-linux-amd64]$ cd ..
[ken@cngb-gigadb-ftp ~]$ rclone version
rclone v1.66.0
- os/version: centos 7.5.1804 (64 bit)
- os/kernel: 3.10.0-862.14.4.el7.x86_64 (x86_64)
- os/type: linux
- os/arch: amd64
- go/version: go1.22.1
- go/linking: static
- go/tags: none
# copy the $bastion-user-pem in local computer and paste it to the ftp server
[ken@cngb-gigadb-ftp ~]$ vi $bastion-user-pem 
# create rclone.conf file with following contents
[ken@cngb-gigadb-ftp ~]$ vi rclone.conf
[aws-efs]
type = sftp
host = $bastion-ip
user = $bastion-user
key_file = /path/to/$bastion-user-pem 
shell_type = unix
md5sum_command = md5sum
sha1sum_command = sha1sum
# use rclone to list the mounted accesspoint in the bastion server
[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf lsd aws-efs:/share
          -1 2024-04-25 10:37:57        -1 config
          -1 2024-04-25 10:37:24        -1 dropbox
[ken@cngb-gigadb-ftp ~]$ 
[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf ls aws-efs:/share/dropbox
[ken@cngb-gigadb-ftp ~]$ 
# use rclone to copy file to the aws efs dropbox in dry-run mode
# user103 is labelled as `COPY TO NEW SERVER`
[ken@cngb-gigadb-ftp ~]$ ls -al /data/data_upload/user103
total 17
drwxrwxr-x   2 gigadb_user gigadb_user 4096 Mar 14 01:55 .
drwxr-xr-x 123 chris              5052 4096 Apr  9 17:14 ..
-rw-r--r--   1 gigadb_user gigadb_user  425 Mar 14 01:47 AD25.txt
-rw-r--r--   1 gigadb_user gigadb_user  425 Mar 14 01:46 NO25.txt
-rw-r--r--   1 gigadb_user gigadb_user  255 Mar 14 01:46 readme.txt
-rw-r--r--   1 root        root         185 Mar 14 01:55 user103.md5
[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf copy -v /data/data_upload/user103 aws-efs:/share/dropbox/user103 --dry-run
2024/04/25 12:30:26 NOTICE: AD25.txt: Skipped copy as --dry-run is set (size 425)
2024/04/25 12:30:26 NOTICE: readme.txt: Skipped copy as --dry-run is set (size 255)
2024/04/25 12:30:26 NOTICE: NO25.txt: Skipped copy as --dry-run is set (size 425)
2024/04/25 12:30:26 NOTICE: user103.md5: Skipped copy as --dry-run is set (size 185)
2024/04/25 12:30:26 NOTICE: 
Transferred:   	    1.260 KiB / 1.260 KiB, 100%, 0 B/s, ETA -
Transferred:            4 / 4, 100%
Elapsed time:         1.4s
[ken@cngb-gigadb-ftp ~]$
# use rclone to copy file to the aws efs dropbox 
[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf copy -v /data/data_upload/user103 aws-efs:/share/dropbox/user103
2024/04/25 12:30:46 INFO  : readme.txt: Copied (new)
2024/04/25 12:30:47 INFO  : AD25.txt: Copied (new)
2024/04/25 12:30:48 INFO  : user103.md5: Copied (new)
2024/04/25 12:30:48 INFO  : NO25.txt: Copied (new)
2024/04/25 12:30:48 INFO  : 
Transferred:   	    1.260 KiB / 1.260 KiB, 100%, 322 B/s, ETA 0s
Transferred:            4 / 4, 100%
Elapsed time:         6.1s

[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf ls aws-efs:/share/dropbox
      425 user103/AD25.txt
      425 user103/NO25.txt
      255 user103/readme.txt
      185 user103/user103.md5
[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf copy -v /data/data_upload/user103 aws-efs:/share/dropbox/user103
2024/04/25 12:32:19 INFO  : There was nothing to transfer
2024/04/25 12:32:19 INFO  : 
Transferred:   	          0 B / 0 B, -, 0 B/s, ETA -
Checks:                 4 / 4, 100%
Elapsed time:         2.1s
[ken@cngb-gigadb-ftp ~]$ 
# use rclone to delete file in the aws efs dropbox in dry-run mode
[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf delete -v aws-efs:/share/dropbox/rclone-current-linux-amd64.zip
2024/04/25 12:15:55 INFO  : rclone-current-linux-amd64.zip: Deleted
[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf delete -v aws-efs:/share/dropbox/rclone-current-linux-amd64.zip
2024/04/25 12:16:00 ERROR : : error listing: directory not found
2024/04/25 12:16:00 ERROR : Attempt 1/3 failed with 2 errors and: directory not found
2024/04/25 12:16:00 ERROR : : error listing: directory not found
2024/04/25 12:16:00 ERROR : Attempt 2/3 failed with 2 errors and: directory not found
2024/04/25 12:16:00 ERROR : : error listing: directory not found
2024/04/25 12:16:00 ERROR : Attempt 3/3 failed with 2 errors and: directory not found
2024/04/25 12:16:00 Failed to delete with 2 errors: last error was: directory not found
# use rclone to delete file in the aws efs dropbox 
[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf delete -v aws-efs:/share/dropbox/user103
2024/04/25 12:34:02 INFO  : NO25.txt: Deleted
2024/04/25 12:34:04 INFO  : readme.txt: Deleted
2024/04/25 12:34:04 INFO  : AD25.txt: Deleted
2024/04/25 12:34:04 INFO  : user103.md5: Deleted
[ken@cngb-gigadb-ftp ~]$ rclone --config rclone.conf ls aws-efs:/share/dropbox
[ken@cngb-gigadb-ftp ~]$ 
[ken@cngb-gigadb-ftp ~]$ ls -al /data/data_upload/user103/
total 17
drwxrwxr-x   2 gigadb_user gigadb_user 4096 Mar 14 01:55 .
drwxr-xr-x 123 chris              5052 4096 Apr  9 17:14 ..
-rw-r--r--   1 gigadb_user gigadb_user  425 Mar 14 01:47 AD25.txt
-rw-r--r--   1 gigadb_user gigadb_user  425 Mar 14 01:46 NO25.txt
-rw-r--r--   1 gigadb_user gigadb_user  255 Mar 14 01:46 readme.txt
-rw-r--r--   1 root        root         185 Mar 14 01:55 user103.md5
[ken@cngb-gigadb-ftp ~]$ 
```