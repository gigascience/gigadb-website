# Gitlab runner

## Setup AWS EC2 instance
* https://www.digitalocean.com/community/tutorials/how-to-keep-ubuntu-22-04-servers-updated

1. Create an AWS EC2 instance with the following configuration:
   - Ubuntu 22.04 LTS
   - At least 2 vCPUs, 4GB of RAM, 100GB of storage
   - Security group allowing SSH (port 22)
   - Region: Asia Pacific (Jakarta) ap-southeast-3
   - Key pair for SSH access (e.g., `id-rsa-aws-jakarta.pem`)
   - Instance type: t3.medium
   - IAM role with permissions to manage EC2 instances and S3 buckets
2. SSH into the instance:
```
$ ssh -i id-rsa-aws-jakarta.pem ubuntu@108.136.186.95
Welcome to Ubuntu 22.04.5 LTS (GNU/Linux 6.8.0-1029-aws x86_64)

 * Documentation:  https://help.ubuntu.com
 * Management:     https://landscape.canonical.com
 * Support:        https://ubuntu.com/pro

 System information as of Tue Aug 12 06:48:56 UTC 2025

  System load:  0.03              Processes:             109
  Usage of /:   1.8% of 96.73GB   Users logged in:       0
  Memory usage: 5%                IPv4 address for ens5: 172.31.47.236
  Swap usage:   0%


Expanded Security Maintenance for Applications is not enabled.

0 updates can be applied immediately.

Enable ESM Apps to receive additional future security updates.
See https://ubuntu.com/esm or run: sudo pro status


The list of available updates is more than a week old.
To check for new updates run: sudo apt update
New release '24.04.3 LTS' available.
Run 'do-release-upgrade' to upgrade to it.


Last login: Tue Aug 12 06:46:59 2025 from 43.218.193.65
To run a command as administrator (user "root"), use "sudo <command>".
See "man sudo_root" for details.

ubuntu@ip-172-31-47-236:~$ 
ubuntu@ip-172-31-47-236:~$ df -hT
Filesystem      Type      Size  Used Avail Use% Mounted on
/dev/root       ext4       97G  1.8G   96G   2% /
tmpfs           tmpfs     1.9G     0  1.9G   0% /dev/shm
tmpfs           tmpfs     768M  856K  767M   1% /run
tmpfs           tmpfs     5.0M     0  5.0M   0% /run/lock
efivarfs        efivarfs  128K  3.6K  120K   3% /sys/firmware/efi/efivars
/dev/nvme0n1p15 vfat      105M  6.1M   99M   6% /boot/efi
tmpfs           tmpfs     384M  4.0K  384M   1% /run/user/1000
ubuntu@ip-172-31-47-236:~$ sudo nano /etc/apt/apt.conf.d/50unattended-upgrades
ubuntu@ip-172-31-47-236:~$ sudo unattended-upgrade --dry-run
ubuntu@ip-172-31-47-236:~$ sudo unattended-upgrade
ubuntu@ip-172-31-47-236:~$ sudo tail -f /var/log/unattended-upgrades/unattended-upgrades.log

```

## Install Docker, Docker Compose
* https://docs.docker.com/engine/install/ubuntu/
* https://www.digitalocean.com/community/questions/how-to-fix-docker-got-permission-denied-while-trying-to-connect-to-the-docker-daemon-socket
* https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-22-04

```
$ docker version
Client: Docker Engine - Community
 Version:           28.3.3
 API version:       1.51
 Go version:        go1.24.5
 Git commit:        980b856
 Built:             Fri Jul 25 11:34:04 2025
 OS/Arch:           linux/amd64
 Context:           default

Server: Docker Engine - Community
 Engine:
  Version:          28.3.3
  API version:      1.51 (minimum version 1.24)
  Go version:       go1.24.5
  Git commit:       bea959c
  Built:            Fri Jul 25 11:34:04 2025
  OS/Arch:          linux/amd64
  Experimental:     false
 containerd:
  Version:          1.7.27
  GitCommit:        05044ec0a9a75232cad458027ca83437aae3f4da
 runc:
  Version:          1.2.5
  GitCommit:        v1.2.5-0-g59923ef
 docker-init:
  Version:          0.19.0
  GitCommit:        de40ad0
ubuntu@ip-172-31-47-236:~$ docker compose version
Docker Compose version v2.38.2-desktop.1
$ docker run hello-world
Unable to find image 'hello-world:latest' locally
latest: Pulling from library/hello-world
17eec7bbc9d7: Pull complete 
Digest: sha256:a0dfb02aac212703bfcb339d77d47ec32c8706ff250850ecc0e19c8737b18567
Status: Downloaded newer image for hello-world:latest

Hello from Docker!
This message shows that your installation appears to be working correctly.

To generate this message, Docker took the following steps:
 1. The Docker client contacted the Docker daemon.
 2. The Docker daemon pulled the "hello-world" image from the Docker Hub.
    (amd64)
 3. The Docker daemon created a new container from that image which runs the
    executable that produces the output you are currently reading.
 4. The Docker daemon streamed that output to the Docker client, which sent it
    to your terminal.

To try something more ambitious, you can run an Ubuntu container with:
 $ docker run -it ubuntu bash

Share images, automate workflows, and more with a free Docker ID:
 https://hub.docker.com/

For more examples and ideas, visit:
 https://docs.docker.com/get-started/


```

## Create dir

```
ubuntu@ip-172-31-47-236:~$ pwd
/home/ubuntu
ubuntu@ip-172-31-47-236:~$ mkdir -p gigadb-website/gigadb/app/tools/gitlab-runner/
ubuntu@ip-172-31-47-236:~$ cd gigadb-website/gigadb/app/tools/gitlab-runner/
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ mkdir scripts
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ ls -al
total 28
drwxrwxr-x 4 ubuntu ubuntu 4096 Aug 19 07:40 .
drwxrwxr-x 3 ubuntu ubuntu 4096 Aug 19 07:32 ..
-rw-r--r-- 1 ubuntu ubuntu  117 Aug 19 07:37 .env
drwxrwxr-x 2 ubuntu ubuntu 4096 Aug 19 07:40 config
-rw-r--r-- 1 ubuntu ubuntu 1127 Aug 19 07:35 docker-compose.yml
-rw-r--r-- 1 ubuntu ubuntu  117 Aug 19 07:35 env-sample
drwxrwxr-x 2 ubuntu ubuntu 4096 Aug 19 07:36 scripts
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ ls -al scripts config
total 12
drwxrwxr-x 2 ubuntu ubuntu 4096 Aug 19 07:36 .
drwxrwxr-x 3 ubuntu ubuntu 4096 Aug 19 07:36 ..
-rw-r--r-- 1 ubuntu ubuntu 1245 Aug 19 07:36 delete_runner_cache.sh
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$

```

## Set value for REGISTRATION_TOKEN

```
$ cp env-sample .env
```

and fill in the value as instructed.

## Register a runner for a team member

You must use the Gitlab user login (GITLAB_USER_LOGIN). Follow the instructions at https://docs.gitlab.com/tutorials/create_register_first_runner/

```
$ docker compose run --rm runner --version
Version:      18.2.1
Git revision: cc489270
Git branch:   18-2-stable
GO version:   go1.24.4 X:cacheprog
Built:        2025-07-28T12:43:39Z
OS/Arch:      linux/amd64
$ docker compose run --rm register register --url https://gitlab.com --token $glrtoken
```

## Start runners

Update ``config/config.toml`` to ensure the value of the ``concurrent``
variables matches the number of ``[[runners]]`` subsections multiplied by the value of the  ``limit`` variable.

then: 

```
$ docker compose up -d runner
$ docker compose logs
```

## Shutdown a runner in standalone Docker engine

Fist, de-register it in Gitlab dashboard, then

```
$ docker compose down -v
```


## Resources 

* https://docs.gitlab.com/runner/configuration/advanced-configuration.html
* https://docs.gitlab.com/ee/ci/runners/configure_runners.html
* https://docs.gitlab.com/runner/register/
* https://docs.gitlab.com/runner/configuration/runner_autoscale_aws/
* https://docs.gitlab.com/runner/install/linux-repository/
* https://docs.gitlab.com/runner/install/docker/
* https://docs.gitlab.com/solutions/cloud/aws/
* https://docs.gitlab.com/runner/configuration/runner_autoscale_aws/

