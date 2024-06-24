# Deployment and configure of the public FTP server

## Prerequisite

* Have followed the steps in `docs/PRODUCTION_DEPLOY.md`, so that the all the infrastructure have been provisioned.
In particular there should now be a new EC2 instance to be used as a file server. The public FTP server is to be deployed on that instance.
Also make sure the Ansible playbook `ops/infrastructure/files_playbook.yml` has been run as well.

* Ensure you have in Gitlab variables `remote_fileserver_hostname` is populated with the domain name for file server for the given environment.

On Upstream, the domain will be files-stg.gigadb.host and files.gigadb.org for the staging and live environment respectively.
On Forks, you will have to create your own domain and create a DNS A record that maps that domain to the EIP associated with the file server, normally `eip-files-staging-username`


## How to deploy from Gitlab pipeline

We have two new jobs (one for building, the other one for deploying) in Gitlab pipeline for each target environment (Staging or Live).
They don't depend on any other jobs.
The build job is the one that need to be trigger manually. The deploy job will happen automatically upon success of the build job.
See below for the build job name for each environment.

### Staging deployment

We use Pure-FTPd and there are two jobs of interest in the Gitlab pipeline of GigaDB: `PureFtpdBuildStaging` in the `staging build` stage, and `PureFtpdDeployStaging` in the `staging deploy` stage.

### Live deployment

There are two jobs of interest in the Gitlab pipeline of GigaDB: `PureFtpdBuildLive` in the `live build` stage, and `PureFtpdDeployLive` in the `staging deploy` stage.

## How to mount the EFS to the file server

Identify the IP addresses needed to connect to the file server and the resource ids to mount the EFS:
```
$ terraform output
bastion_ec2_type = "t3.small"
ec2_bastion_private_ip = "10.97.0.225"
ec2_bastion_public_ip = "13.35.67.79"
ec2_files_private_ip = "10.97.0.123"
ec2_private_ip = "10.97.0.67"
ec2_public_ip = "13.37.101.63"
efs_filesystem_access_points = {
  "configuration_area" = "fsap-xxxx"
  "dropbox_area" = "fsap-yyyy"
}
...
efs_filesystem_id = "fs-zzzzzz"
...
```

Log in to the file server with SSH using the value of `ec2_bastion_public_ip` and `ec2_files_private_ip` as below:
```
$ ssh -i /path/to/your/secret.pem -o ProxyCommand="ssh -W %h:%p -i /path/to/your/secret.pem  centos@13.35.67.79" centos@10.97.0.123
```

Mount the EFS file system for /share/dropbox and /share/config using the values gathered by `terraform output`:
```
$ sudo mount -t efs -o tls,accesspoint=fsap-yyyy fs-zzzzzz /share/dropbox 
$ sudo mount -t efs -o tls,accesspoint=fsap-xxxx fs-zzzzzz /share/config
$ df -T -h
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/config
127.0.0.1:/    nfs4      8.0E     0  8.0E   0% /share/dropbox
```

## How to start and stop the server

on the file server, there is a docker compose file at `/home/centos/docker-compose.yml`.
Start the server with:
```
$ docker compose up -d
$ docker compose logs ftpd
```

To tear down the server:
```
$ docker compose down -v --remove-orphans
```

## How to add user accounts

You can create a user account with the following command:
```
$ docker compose exec ftpd pure-pw useradd user0 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u dropboxuser -d /home/user0
Password: 
Enter it again: 
$ docker compose exec ftpd pure-pw show user0 -f /etc/pure-ftpd/passwd/pureftpd.passwd
```

>**Note**: Make sure the argument after the `-d` parameter start with `/home` to ensure the account use the mounted EFS

## Confirm the passwd file is in /share/config/pure-ftpd/passwd/ and updated
```
$ ls /share/config/pure-ftpd/passwd
pureftpd.passwd
$ cat /share/config/pure-ftpd/passwd/pureftpd.passwd 
```

## Test the ftp server

And test that it works by uploading and then downloading something to/from that account
```
$ ncftpput -u user0 $ec2_files_public_ip or $remote_fileserver_hostname / /path/to/some/file
Password: **********
Remote host has closed the connection.
.../path/to/some/file:   72.00 kB  207.43 kB/s 
$ ncftpget -u user0 $ec2_files_public_ip or $remote_fileserver_hostname ./ /file
OR
$ ncftp
ncftp> open -u [username] -p [password] [hostname-or-ip-address]
Connecting to $ec2_files_public_ip...                                                                                                                                                                                                        
--------- Welcome to Pure-FTPd [privsep] [TLS] ----------
You are user number 1 of 5 allowed.
Local time is now 08:42. Server port: 21.
This is a private system - No anonymous login
IPv6 connections are also welcome on this server.
You will be disconnected after 15 minutes of inactivity.
Logging in...                                                                                                                                                                                                                      
OK. Current directory is /
Logged in to $ec2_files_public_ip.                                                                                                                                                                                                           
ncftp / > ls
$file
ncftp / > get $file
$file:                                   72.00 kB  338.48 kB/s 
ncftp / > quit
# This will download the file to your current local directory.
```
