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
* https://docs.docker.com/desktop/setup/install/linux/ubuntu/
* https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-22-04

```
ubuntu@ip-172-31-47-236:~$ docker --version
Docker version 28.3.3, build 980b856
ubuntu@ip-172-31-47-236:~$ docker compose version
Docker Compose version v2.38.2-desktop.1
```

## Set value for REGISTRATION_TOKEN

```
$ cp env-sample .env
```

and fill in the value as instructed.

## Register a runner for a team member

You must use the Gitlab user login (GITLAB_USER_LOGIN).

```
$ docker-compose run --rm -e RUNNER_TAG_LIST="<Gitlab user login here>" register
```

## Start runners

Update ``config/config.toml`` to ensure the value of the ``concurrent``
variables matches the number of ``[[runners]]`` subsections multiplied by the value of the  ``limit`` variable.

then: 

```
$ docker-compose up -d runner
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

