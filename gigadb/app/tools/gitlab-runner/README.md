# Gitlab runner

This document describes how to set up a self-managed Gitlab runner on an AWS EC2 instance using Docker and Docker Compose. 
It is based on the official Gitlab runner Docker image and the Gitlab runner [documentation](https://docs.gitlab.com/runner/).


## Setup AWS EC2 instance
* https://www.digitalocean.com/community/tutorials/how-to-keep-ubuntu-22-04-servers-updated

1. Create an AWS EC2 instance with the following configuration:
   - Ubuntu 22.04 LTS
   - At least 2 vCPUs, 4GB of RAM, 100GB of storage
   - Security group allowing SSH (port 22)
   - Region: Asia Pacific (Jakarta) ap-southeast-3
   - Key pair for SSH access (e.g., `id-rsa-aws-jakarta.pem`)
   - Instance type: t3.medium
2. The instance's public IP `gitlab_runner_aws_ec2_public_ip` and the ssh cert `id-rsa-aws-jakarta.pem` are stored in Gitlab cnhk-infra variables [page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd#js-cicd-variables-settings).
3. SSH into the instance:
```
$ ssh -i id-rsa-aws-jakarta.pem ubuntu@$gitlab_runner_aws_ec2_public_ip
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
* https://docs.docker.com/engine/install/ubuntu/#install-using-the-repository
* 
```
$ sudo apt update
$ sudo apt-get install ca-certificates curl
$ sudo install -m 0755 -d /etc/apt/keyrings
$ sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
$ sudo chmod a+r /etc/apt/keyrings/docker.asc
$ echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
$ sudo apt-get update
$ sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
$ sudo systemctl status docker
● docker.service - Docker Application Container Engine
     Loaded: loaded (/lib/systemd/system/docker.service; enabled; vendor preset: enabled)
     Active: active (running) since Tue 2025-09-02 02:41:12 UTC; 20s ago
TriggeredBy: ● docker.socket
       Docs: https://docs.docker.com
   Main PID: 2584 (dockerd)
      Tasks: 9
     Memory: 22.1M
        CPU: 342ms
     CGroup: /system.slice/docker.service
             └─2584 /usr/bin/dockerd -H fd:// --containerd=/run/containerd/containerd.sock

Sep 02 02:41:11 ip-172-31-46-156 dockerd[2584]: time="2025-09-02T02:41:11.424214470Z" level=info msg="detected 127.0.0.53 nameserver, assuming systemd-resolved, so using resolv.conf: /run/systemd/resolve/resolv.conf"
Sep 02 02:41:11 ip-172-31-46-156 dockerd[2584]: time="2025-09-02T02:41:11.472205562Z" level=info msg="Creating a containerd client" address=/run/containerd/containerd.sock timeout=1m0s
Sep 02 02:41:11 ip-172-31-46-156 dockerd[2584]: time="2025-09-02T02:41:11.567536973Z" level=info msg="Loading containers: start."
Sep 02 02:41:12 ip-172-31-46-156 dockerd[2584]: time="2025-09-02T02:41:12.237600254Z" level=info msg="Loading containers: done."
Sep 02 02:41:12 ip-172-31-46-156 dockerd[2584]: time="2025-09-02T02:41:12.263950936Z" level=info msg="Docker daemon" commit=bea959c containerd-snapshotter=false storage-driver=overlay2 version=28.3.3
Sep 02 02:41:12 ip-172-31-46-156 dockerd[2584]: time="2025-09-02T02:41:12.264054411Z" level=info msg="Initializing buildkit"
Sep 02 02:41:12 ip-172-31-46-156 dockerd[2584]: time="2025-09-02T02:41:12.308553396Z" level=info msg="Completed buildkit initialization"
Sep 02 02:41:12 ip-172-31-46-156 dockerd[2584]: time="2025-09-02T02:41:12.316697342Z" level=info msg="Daemon has completed initialization"
Sep 02 02:41:12 ip-172-31-46-156 dockerd[2584]: time="2025-09-02T02:41:12.316761871Z" level=info msg="API listen on /run/docker.sock"
Sep 02 02:41:12 ip-172-31-46-156 systemd[1]: Started Docker Application Container Engine.

$ sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
$ docker --version
Docker version 28.3.3, build 980b856
u$ docker compose version
Docker Compose version v2.39.1
$ docker run hello-world
docker: permission denied while trying to connect to the Docker daemon socket at unix:///var/run/docker.sock: Head "http://%2Fvar%2Frun%2Fdocker.sock/_ping": dial unix /var/run/docker.sock: connect: permission denied

Run 'docker run --help' for more information
$ sudo usermod -aG docker ${USER}
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

## Create dir structure in the EC2 ubuntu server

```
% ssh -i ~/.ssh/id-rsa-aws-jakarta.pem ubuntu@$gitlab_runner_aws_ec2_public_ip
ubuntu@ip-172-31-47-236:~$ pwd
/home/ubuntu
ubuntu@ip-172-31-47-236:~$ mkdir -p gigadb-website/gigadb/app/tools/gitlab-runner/
ubuntu@ip-172-31-47-236:~$ cd gigadb-website/gigadb/app/tools/gitlab-runner/
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ mkdir -p scripts config
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$

```

## Copy files to the EC2 instance

```
% cd gigadb-website/gigadb/app/tools/gitlab-runner/
% scp -i ~/.ssh/id-rsa-aws-jakarta.pem . ubuntu@$gitlab_runner_aws_ec2_public_ip:~/gigadb-website/gigadb/app/tools/gitlab-runner/
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ ls -al
total 28
drwxrwxr-x 4 ubuntu ubuntu 4096 Aug 19 07:40 .
drwxrwxr-x 3 ubuntu ubuntu 4096 Aug 19 07:32 ..
drwxrwxr-x 2 ubuntu ubuntu 4096 Aug 19 07:40 config
-rw-r--r-- 1 ubuntu ubuntu 1127 Aug 19 07:35 docker-compose.yml
drwxrwxr-x 2 ubuntu ubuntu 4096 Aug 19 07:36 scripts
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ ls -al scripts config
total 12
drwxrwxr-x 2 ubuntu ubuntu 4096 Aug 19 07:36 .
drwxrwxr-x 3 ubuntu ubuntu 4096 Aug 19 07:36 ..
-rw-r--r-- 1 ubuntu ubuntu 1245 Aug 19 07:36 delete_runner_cache.sh
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$
```

## Create a Gitlab Project runner per project

Details can be referred to https://docs.gitlab.com/ci/runners/runners_scope/#project-runners.

Prerequisites:

You must have the Maintainer role for the project.

To create a project runner:
1. On the left sidebar, select Search or go to and find your project.
2. Go to Settings > CI/CD > Runners > Expand.
3. Select New project runner.
4. Select the operating system where GitLab Runner is installed.
5. In the Tags section, uncheck `Run untagged jobs`. In the Tags field, enter the job tags the runner is allowed to run (e.g., `$GITLAB_USER_LOGIN`). Ensure every job that should target this runner includes tags, for example:
```
  tags:
    - $GITLAB_USER_LOGIN
```
6. Select Create runner.
7. Then you will see the registration token, e.g., glrt-xxx-xxxxxxxxxxxxxxxxxxxx
8. Run the following command to register the runner (replace with the actual token) in the EC2 instance:

```
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ docker compose run register register --url https://gitlab.com --token glrt-xxx-xxxxxxxxxxxxxxxxxxxx
Enter the GitLab instance URL (for example, https://gitlab.com/):
[https://gitlab.com]: 
Verifying runner... is valid                        correlation_id=2a6c5e5cd66b03587ed5df2fffc418bc runner=Js9yGlCf1
Enter a name for the runner. This is stored only in the local config.toml file:
[1b8a906f342a]: cicd-bot-kencho18
Enter an executor: shell, virtualbox, docker-windows, kubernetes, docker-autoscaler, instance, custom, ssh, parallels, docker, docker+machine:
docker
Enter the default Docker image (for example, ruby:3.3):
alpine:latest
Runner registered successfully. Feel free to start it, but if it's running already the config should be automatically reloaded!
 
Configuration (with the authentication token) was saved in "/etc/gitlab-runner/config.toml"
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ sudo cat config/config.toml 
concurrent = 1
check_interval = 0
shutdown_timeout = 0

[session_server]
  session_timeout = 1800

[[runners]]
  name = "cicd-bot-kencho18"
  url = "https://gitlab.com"
  id = 49583611
  token = "glrt-xxx-xxxxxxxxxxxxxxxxxxxx"
  token_obtained_at = 2025-08-22T08:22:14Z
  token_expires_at = 0001-01-01T00:00:00Z
  executor = "docker"
  [runners.cache]
    MaxUploadedArchiveSize = 0
    [runners.cache.s3]
    [runners.cache.gcs]
    [runners.cache.azure]
  [runners.docker]
    tls_verify = false
    image = "alpine:latest"
    privileged = false
    disable_entrypoint_overwrite = false
    oom_kill_disable = false
    disable_cache = false
    volumes = ["/cache"]
    shm_size = 0
    network_mtu = 0
```

9. Update the `config/config.toml` file to run Docker-in-Docker (DinD) and multiple jobs.

```
concurrent = 10
check_interval = 10
log_level = "info"
log_format = "runner"
connection_max_age = "15m0s"
shutdown_timeout = 0

[[runners]]
  name = "cicd-bot-kencho18"
  limit = 5
  url = "https://gitlab.com"
  id = 49583611
  token = "glrt-xxx-xxxxxxxxxxxxxxxxxxxx"
  token_obtained_at = 2025-08-23T07:43:34Z
  token_expires_at = 0001-01-01T00:00:00Z
  executor = "docker"
  [runners.cache]
    MaxUploadedArchiveSize = 0
    [runners.cache.s3]
    [runners.cache.gcs]
    [runners.cache.azure]
  [runners.docker]
    tls_verify = false
    image = "alpine:latest"
    privileged = true
    disable_entrypoint_overwrite = false
    oom_kill_disable = false
    disable_cache = true
    volumes = ["/var/runner/cache:/cache:rw", "/var/runner/builds:/builds:rw"]
    pull_policy = ["if-not-present"]
    shm_size = 0
    network_mtu = 0
```
10. If there is more than one project needs a runner, repeat steps 1-9 to create a project runner for each project with a different registration token.

11. Spin up the runner:

```
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ docker compose up -d runner
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ $ docker compose ps -a
NAME                                      IMAGE                         COMMAND                  SERVICE    CREATED      STATUS                  PORTS
gitlab-runner-runner-1                    gitlab/gitlab-runner:latest   "/usr/bin/dumb-init …"   runner     2 days ago   Up 2 days
```

12. The runner should now be active in the Gitlab project under Settings > CI/CD > Project Runners.
13. In case for the upstream projects, then go to the project that you want to use this runner, then go to Settings > CI/CD > Project Runners > Enable for this project.
14. Runner's details can be seen in the Runner dashboard by clicking the runner, e.g., https://gitlab.com/gigascience/forks/kencho-gigadb-website/-/runners/49583611
15. Trigger a pipeline in the project to test the runner, you will see the pipeline job is executed by the runner with the id stated in the `config/config.toml`, description you added in when creating a runner, eg. `aws_ec2_runner`, and runner name `cicd-bot-kencho18` you provided when registering the runner. 


## Monitor the runner status and logs:
```
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ docker compose logs
runner-1  | Runtime platform                                    arch=amd64 os=linux pid=6 revision=9ba718cd version=18.3.0
runner-1  | Starting multi-runner from /etc/gitlab-runner/config.toml...  builds=0 max_builds=0
runner-1  | Running in system-mode.                            
runner-1  |                                                    
runner-1  | Usage logger disabled                               builds=0 max_builds=10
runner-1  | Configuration loaded                                builds=0 max_builds=10
runner-1  | listen_address not defined, metrics & debug endpoints disabled  builds=0 max_builds=10
runner-1  | [session_server].listen_address not defined, session endpoints disabled  builds=0 max_builds=10
runner-1  | Initializing executor providers                     builds=0 max_builds=10
runner-1  | Checking for jobs... received                       correlation_id=0994bf088971459b96d42ef9927c181f job=11115201673 repo_url=https://gitlab.com/gigascience/forks/kencho-gigadb-website.git runner=wn6-rPMzo
runner-1  | Added job to processing list                        builds=1 job=11115201673 max_builds=10 project=29922929 queue_depth=1 queue_size=1 repo_url=https://gitlab.com/gigascience/forks/kencho-gigadb-website.git time_in_queue_seconds=1
runner-1  | Appending trace to coordinator...ok                 code=202 correlation_id=d3ddfb0b09c94ebb9e1c43bd210c6ec6 job=11115201673 job-log=0-777 job-status=running runner=wn6-rPMzo sent-log=0-776 status=202 Accepted update-interval=1m0s
runner-1  | Appending trace to coordinator...ok                 code=202 correlation_id=8d85c05f175e44d888fe32c8f29bc7f1 job=11115201673 job-log=0-10916 job-status=running runner=wn6-rPMzo sent-log=777-10915 status=202 Accepted update-interval=3s
.....
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ 
```


## Shutdown and remove a runner, if needed

If the runner is no longer needed, you can remove it from the Gitlab project in the gitlab runner dashboard.
You can also stop containers and remove containers, networks, volumes by running the following command in the EC2 instance:

```
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ docker compose down -v
```

## Examine and manage the runner cache

The runner cache is stored in the `/var/runner/cache` directory on the host machine. You can examine the cache files by running the following command in the EC2 instance:

```
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ $ ls -al /var/runner/cache/
total 12
drwxr-xr-x 3 root   root   4096 Aug 23 07:54 .
drwxr-xr-x 4 root   root   4096 Aug 22 08:08 ..
drwxr-xr-x 3 ubuntu ubuntu 4096 Aug 26 07:31 gigascience
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ ls -la /var/runner/cache/gigascience/
total 16
drwxr-xr-x 3 ubuntu ubuntu 4096 Aug 26 07:31 .
drwxr-xr-x 3 root   root   4096 Aug 23 07:54 ..
drwx------ 3 root   root   4096 Aug 26 07:31 forks
drwx------ 3 root root 4096 Aug 26 07:31 upstream
drwx------ 3 root root 4096 Aug 26 07:31 upstream
```

The cache will grow over time as more jobs are run which will eat up the server's disk space. You can delete the cache files by running the following command in the EC2 instance:

```
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ sudo rm -rf /var/runner/cache/*
```   

A dedicated script `scripts/delete_runner_cache.sh` is also provided to delete the cache files, which will executed in `/usr/local/bin/delete_runner_cache.sh`. You can run the script by executing the following command in the EC2 instance:

```
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ ls -al /usr/local/bin/delete_runner_cache.sh 
-rwxr-xr-x 1 root root 1259 Aug 26 06:15 /usr/local/bin/delete_runner_cache.sh
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$  ls -la /var/log/gitlab-runner/delete_runner_cache.log
-rw-r--r-- 1 ubuntu ubuntu 5736 Aug 26 06:53 /var/log/gitlab-runner/delete_runner_cache.log
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ ls -la /var/runner/cache/gigascience/forks
total 16
drwxr-xr-x 4 ubuntu ubuntu 4096 Aug 26 06:22 .
drwxr-xr-x 3 ubuntu ubuntu 4096 Aug 26 06:19 ..
drwxr-xr-x 4 ubuntu ubuntu 4096 Aug 26 07:22 kencho-gigadb-website
drwxr-xr-x 3 ubuntu ubuntu 4096 Aug 26 06:22 pl-gigadb-website
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ /usr/local/bin/delete_runner_cache.sh 
[2025-08-26 07:31:18] === Starting GitLab Runner Cache Cleanup ===
[2025-08-26 07:31:18] Disk usage before cleanup:
105M    /var/runner/cache/gigascience/forks/pl-gigadb-website/develop-12-non_protected
105M    /var/runner/cache/gigascience/forks/pl-gigadb-website
164K    /var/runner/cache/gigascience/forks/kencho-gigadb-website/0_composer_ops/scripts/package-lock-1fb660e43ad49f364b7447f9c4caae95a2ed1cf7-191-non_protected
168K    /var/runner/cache/gigascience/forks/kencho-gigadb-website/0_composer_ops/scripts
172K    /var/runner/cache/gigascience/forks/kencho-gigadb-website/0_composer_ops
105M    /var/runner/cache/gigascience/forks/kencho-gigadb-website/create-gitlab-runner-in-aws-ec2-191-non_protected
105M    /var/runner/cache/gigascience/forks/kencho-gigadb-website
210M    /var/runner/cache/gigascience/forks
210M    /var/runner/cache/gigascience
[2025-08-26 07:31:18] Items to be deleted: 11
[2025-08-26 07:31:18] Starting deletion process...
[2025-08-26 07:31:18] Cache cleanup completed successfully
[2025-08-26 07:31:18] Disk usage after cleanup:
4.0K    /var/runner/cache/gigascience
[2025-08-26 07:31:18] Current disk space:
Filesystem      Size  Used Avail Use% Mounted on
/dev/root        97G   38G   60G  39% /
[2025-08-26 07:31:18] === Cache Cleanup Process Finished ===

```

A cron job is also set up to run the script every Sunday Midnight. You can check the cron job by running the following command in the EC2 instance:

```
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ crontab -l
# Edit this file to introduce tasks to be run by cron.
# 
# Each task to run has to be defined through a single line
# indicating with different fields when the task will be run
# and what command to run for the task
# 
# To define the time you can provide concrete values for
# minute (m), hour (h), day of month (dom), month (mon),
# and day of week (dow) or use '*' in these fields (for 'any').
# 
# Notice that tasks will be started based on the cron's system
# daemon's notion of time and timezones.
# 
# Output of the crontab jobs (including errors) is sent through
# email to the user the crontab file belongs to (unless redirected).
# 
# For example, you can run a backup of all your user accounts
# at 5 a.m every week with:
# 0 5 * * 1 tar -zcf /var/backups/home.tgz /home/
# 
# For more information see the manual pages of crontab(5) and cron(8)
# 
# m h  dom mon dow   command
0 0 * * 7 /usr/local/bin/delete_runner_cache.sh
```

## Distributed runners caching in Wasabi S3
https://docs.gitlab.com/runner/configuration/autoscale/#distributed-runners-caching

To enable distributed runners caching in Wasabi S3, you need to set up a Wasabi S3 bucket and configure the Gitlab runner to use it for caching.

1. Create a Wasabi S3 bucket, e.g., `gitlab-runner`.
2. Create an IAM user `gitlab` with programmatic access and attach a policy `AllowReadWriteGitLabRunner` to allow access to the bucket.
3. Obtain the Access Key and Secret Key for the IAM user, e.g., `WASABI_GITLAB_ACCESS_KEY` and `WASABI_GITLAB_SECRETS_KEY`, which are stored in Gitlab cnhk-infra variables [page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd#js-cicd-variables-settings).
4. Update the `config/config.toml` file to use the Wasabi S3 bucket for caching as shown in step 9 above.
```
[runners.cache]
    Type = "s3"
    Path = "/cache"
    Shared = false
    [runners.cache.s3]
        BucketName = "gitlab-runner"
        BucketLocation = "ap-northeast-1"
        Insecure = false
        ServerAddress = "s3.ap-northeast-1.wasabisys.com"
        AccessKey = "$WASABI_GITLAB_ACCESS_KEY"
        SecretKey = "$WASABI_GITLAB_SECRETS_KEY"
[runners.docker]
    tls_verify = false
    image = "alpine:latest"
    privileged = true
    disable_entrypoint_overwrite = false
    oom_kill_disable = false
    volumes = ["/var/runner/cache:/cache:rw", "/var/runner/builds:/builds:rw"]
    pull_policy = ["if-not-present"]
    shm_size = 0
    network_mtu = 0
```
6. Restart the Gitlab runner to apply the changes:

```
ubuntu@ip-172-31-47-236:~/gigadb-website/gigadb/app/tools/gitlab-runner$ docker compose restart runner
```
7. Verify that the runner is using the Wasabi S3 bucket for caching by checking the pipeline log, you should see messages like the following when a job is running at the first time:
```   
Restoring cache
00:01
Checking cache for 0_composer_ops/scripts/package-lock-1fb660e43ad49f364b7447f9c4caae95a2ed1cf7-201-non_protected...
WARNING: file does not exist                       
Failed to extract cache
...
Uploading cache.zip to https://s3.ap-northeast-1.wasabisys.com/gitlab-runner/cache/runner/wn6-rPMzo/project/29922929/0_composer_ops/scripts/package-lock-1fb660e43ad49f364b7447f9c4caae95a2ed1cf7-201-non_protected 
Created cache
```
8. When the job is run again, you should see messages like the following indicating that the cache is being restored from the Wasabi S3 bucket:
```
Restoring cache
Checking cache for 0_composer_ops/scripts/package-lock-1fb660e43ad49f364b7447f9c4caae95a2ed1cf7-201-non_protected...
Downloading cache from https://s3.ap-northeast-1.wasabisys.com/gitlab-runner/cache/runner/wn6-rPMzo/project/29922929/0_composer_ops/scripts/package-lock-1fb660e43ad49f364b7447f9c4caae95a2ed1cf7-201-non_protected  ETag="409952bfc6fd033a6a6c339f79d58a0e"
Successfully extracted cache
...
Archive is up to date!                             
Created cache...
```
9. You can also check the Wasabi S3 bucket to see if the cache files are being uploaded.


## Resources 

* https://docs.gitlab.com/runner/configuration/autoscale/#distributed-runners-caching
* https://docs.gitlab.com/tutorials/create_register_first_runner/
* https://docs.gitlab.com/runner/configuration/advanced-configuration.html
* https://docs.gitlab.com/ee/ci/runners/configure_runners.html
* https://docs.gitlab.com/runner/register/
* https://docs.gitlab.com/runner/configuration/runner_autoscale_aws/
* https://docs.gitlab.com/runner/install/linux-repository/
* https://docs.gitlab.com/runner/install/docker/
* https://docs.gitlab.com/solutions/cloud/aws/
* https://docs.gitlab.com/runner/configuration/runner_autoscale_aws/

