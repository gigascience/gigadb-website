# Troubleshooting guide for the GigaDB Website deployment

This page provides solution for the problems when deploying the production GigaDB Website.

### How to check the servers' status

The status of GigaDB servers can be checked from the UptimeRobot [Dashboard](https://stats.uptimerobot.com/LGVQXSkN1y). 
The setup of UptimeRobot page can refer to this [doc](../UPTIME_STATUS_PAGE.md).

### How to access GigaDB Website Tideways dashboard

[Tideways](https://tideways.com/) is used to monitor, profile and track exceptions of GigaDB Website.
After login [Tideways](https://app.tideways.io/login) with the credentials for `tech@gigasciencejournal.com`, you would see a dashboard of `Gigadb` organization with a service monitoring the staging and live of `gigascience/upstream/gigadb-website`.

### How to access GigaDB Website Grafana dashboard

The system resources and performance of GigaDB servers are monitored by [Prometheus](https://prometheus.io/) and [Grafana](https://grafana.com/),
these two tools work together to detect and alert about possible errors, eg. disk full, low memory utilization.
The details of GigaDB monitoring system implementation, please refer to this [doc](MONITORING.md).

Here is the grafana [dashboard](http://monitoring.gigadb.host:3000/login), the login credentials can be obtained from [here](https://gitlab.com/groups/gigascience/-/settings/ci_cd).

Before you try to reach the grafana dashboard, your computer's IP has to be first  added into the security group of the monitoring instance in the AWS [dashboard](https://ap-east-1.console.aws.amazon.com/ec2/home?region=ap-east-1#Home:)

### How to manage the containers for deploying GigaDB Website?

The production containers can be managed through the live [portainer dashboard](https://portainer.gigadb.org/) or the staging [portainer dashboard](https://portainer.staging.gigadb.org/).
The login credentials can be found at the gitlab variable page.
The details of portainer can be found at [here](https://github.com/portainer/portainer).

### How to login the webapp server via ssh

The webapp server can only be accessed through the bastion server for security reason, details as below:
```
# in ops/infrastructure/envs/live dir
# get all IP information of the servers
$ terraform output
ec2_bastion_private_ip = "xx.xx.x.xx"
ec2_bastion_public_ip "x.xx.xxx.xxx"
ec2_private_ip = "yy.yy.y.yyy"
ec2_public_ip = "yy.yyy.yy.yyy"
rds_instance_address = "rds-server-live-gigadb.xxxxxxxxx.ap-east-1.rds.amaxonaws.com"
vpc_database_subnet_group = "vpc-ap-east-1-live-gigadb-gigadb"
vpc_id = "vpc-xxxxxxxxxxxxxxxx"
# login webapp server
$ ssh -i path/to/id-rsa-aws-hk-gigadb.pem -o ProxyCommand='ssh -i ~path/to/id-rsa-aws-hk-gigadb.pem -W %h:%p centos@$ec2_bastion_public_ip' centos@$ec2_private_ip
Activate the web console with: systemctl enable --now cockpit.socket

Last login: Tue Oct 10 02:11:34 2023 from 10.99.0.86
[centos@ip-10-99-0-235 ~]$ ls
app_data
```

### How to renew an expiring TLS certificate

The details of TLS setup can refer to the [TLS doc](../TLS.md). Currently, the 
Let's Encrypt's certificate only last for 90 days, and Let's Encrypt's will 
start sending out reminder emails of renewing 30 days before expiration.

```
# login webapp server
$ ssh -i path/to/id-rsa-aws-hk-gigadb.pem -o ProxyCommand='ssh -i ~path/to/id-rsa-aws-hk-gigadb.pem -W %h:%p centos@$ec2_bastion_public_ip' centos@$ec2_private_ip
$ docker ps -a
CONTAINER ID   IMAGE                                                                                     COMMAND                  CREATED      STATUS      PORTS                                                                                            NAMES
2798970abde7   registry.gitlab.com/gigascience/upstream/gigadb-website/production_tideways-daemon:live   "tideways-daemon --h…"   6 days ago   Up 6 days   9135/tcp                                                                                         gigadb-website_tideways-daemon_1
3c0f5e74354d   portainer/portainer-ce:latest                                                             "/portainer -H unix:…"   6 days ago   Up 6 days   0.0.0.0:8000->8000/tcp, :::8000->8000/tcp, 9443/tcp, 0.0.0.0:9009->9000/tcp, :::9009->9000/tcp   gigadb-website_portainer_1
29818e4c5880   registry.gitlab.com/gigascience/upstream/gigadb-website/production_web:live               "/docker-entrypoint.…"   6 days ago   Up 6 days   0.0.0.0:80->80/tcp, :::80->80/tcp, 0.0.0.0:443->443/tcp, :::443->443/tcp                         gigadb-website_web_1
e8a6f6681587   registry.gitlab.com/gigascience/upstream/gigadb-website/production_app:live               "docker-php-entrypoi…"   6 days ago   Up 6 days   9000/tcp, 9135/tcp                                                                               gigadb-website_application_1                    
$ docker exec gigadb-website_web_1 ls -l /etc/letsencrypt
total 4
drwx------    3 root     root            42 Sep 26 16:04 accounts
drwx------    3 root     root            29 Sep 26 16:04 archive
-rw-r--r--    1 root     root          1008 Sep 26 16:03 cli.ini
drwx------    3 root     root            43 Sep 26 16:04 live
drwxr-xr-x    2 root     root            34 Sep 26 16:04 renewal
drwxr-xr-x    5 root     root            43 Sep 26 16:04 renewal-hooks
$ docker exec gigadb-website_web_1 ls -1l /etc/letsencrypt/live
total 4
-rw-r--r--    1 root     root           740 Sep 26 16:04 README
drwxr-xr-x    2 root     root            93 Sep 26 16:04 gigadb.org
$ docker exec gigadb-website_web_1 ls -1l /etc/letsencrypt/live/gigadb.org
total 4
-rw-r--r--    1 root     root           692 Sep 26 16:04 README
lrwxrwxrwx    1 root     root            39 Sep 26 16:04 cert.pem -> ../../archive/gigadb.org/cert1.pem
lrwxrwxrwx    1 root     root            40 Sep 26 16:04 chain.pem -> ../../archive/gigadb.org/chain1.pem
lrwxrwxrwx    1 root     root            44 Sep 26 16:04 fullchain.pem -> ../../archive/gigadb.org/fullchain1.pem
lrwxrwxrwx    1 root     root            42 Sep 26 16:04 privkey.pem -> ../../archive/gigadb.org/privkey1.pem
$ docker volume ls
DRIVER    VOLUME NAME
local     gigadb-website_assets
local     gigadb-website_feeds
local     gigadb-website_le_config
local     gigadb-website_le_webrootpath
local     gigadb-website_portainer_data
```

On the Gitlab pipeline page for Upstream gigadb-website project, click `ld_teardown`
in  the live deploy stage to stop and remove running containers, networks, 
volumes, and images.

Then delete the existing certs `tls_chain_pem:live`, `tls_fullchain_pem:live` 
and `tls_privkey_pem:live` in the gitlab [upstream variable page](https://gitlab.com/gigascience/upstream/gigadb-website/-/settings/ci_cd).
After that, manually start the `ld_gigadb` in the last successful pipeline in 
gitlab and you will see screen output likes this:
```
.
.
.
$ ./ops/scripts/setup_cert.sh
Checking whether the certificate exists locally
cert_files_local_exists: false
To see if they could be found in gitlab
fullchain_pem_remote_exists: false
privkey_pem_remote_exists: false
chain_pem_remote_exists: false
Certs do not exist in the filesystem
No certs on GitLab, certbot to create one
Running certbot to make new cert
Saving debug log to /var/log/letsencrypt/letsencrypt.log
.
.
.
```
Once the `ld_gigadb` is finished successfully, the `https://gigadb.org` will be up and running again.
The certificates will be renewed and be saved to the gitlab variable page.


### How to create a user in the bastion server

The bastion server can only be accessed from the authorized users, and each user can be created by following the steps below:
```
$ cd ops/infrastructure/envs/live
# try to create user lily in the bastion server
$ ansible-playbook -i ../../inventories users_playbook.yml -e "newuser=lily"
# update the permission of the private key
$ chmod 500 output/privkeys-$bastion-ip/lily
$ ls -Al output/privkeys-$bastion-ip
total 8
-r-x------@ 1 kencho  staff  3357 Jul  3 14:39 lily
# login bastion server
$ ssh -i /path/to/envs/live/output/privkeys-$bastion-ip/lily lily@$bastion-ip
[lily@ip-10-99-0-183 ~]$ ls
uploadDir
```
### What to do if we receive disk usage alerts or pipeline jobs fail due lack of space on device

The disk usage is usually consumed by the docker containers, and it can be released by following the steps below:
```
# identify which server is firing the alert
$ cd ops/infrastructure/envs/live
# get all IP information of the servers
$ terraform output
# release usage in bastion server
$ ssh -i ~path/to/id-rsa-aws-hk-gigadb.pem centos@$ec2_bastion_public_ip
activate the web console with: systemctl enable --now cockpit.socket
.
.
[centos@ip-10-99-0-235 ~]$ docker systems prune --all
# release usage in webapp server
$ ssh -i path/to/id-rsa-aws-hk-gigadb.pem -o ProxyCommand='ssh -i ~path/to/id-rsa-aws-hk-gigadb.pem -W %h:%p centos@$ec2_bastion_public_ip' centos@$ec2_private_ip
Activate the web console with: systemctl enable --now cockpit.socket
.
.
[centos@ip-10-99-0-216 ~]$ docker systems prune --all
```

After a while, a `Resolved` email will be sent to `tech@gigasciencejournal.com`.

### How to deploy a previous version of the software if the current deployment has major bugs

1. Go to GitLab Upstream [pipeline page](https://gitlab.com/gigascience/upstream/gigadb-website/-/pipelines).
2. Look for the last successful pipeline (all green), and click into it.
3. Click `build_live`, and then `ld_live` to re-deploy the production website to the previous version.


### What to do if terraform execution fails

1. Make sure `terraform` is installed, and here is the [installation guide](https://developer.hashicorp.com/terraform/downloads).
2. Make sure `terraform` get the correct upstream terraform state from the GitLab by confirming the output with `terraform output`.
If you are not the developer that instantiated a current running GigaDB application, then the `live` directory will be empty and no outputs will be displayed:
```
$ terraform output
╷
│ Warning: No outputs found
```

In this case, run the command to copy terraform files to live environment:
```
$ ../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env live
You need to specify the path to the ssh private key to use to connect to the EC2 instance: ~/.ssh/id-rsa-aws-hk-gigadb.pem
You need to specify your GitLab username: <your gitlab username>
You need to specify an AWS region: ap-east-1
```

Running `terraform output` should now display the various IP addresses.

>If you get the following error message after executing `terraform output`:
```
│ Error: Backend initialization required: please run "terraform init"
│ 
│ Reason: Backend configuration block has changed
```

>Then this command can fix the error:
```
$ export GITLAB_ACCESS_TOKEN=<GigaDB GITLAB ACCESS TOKEN>
terraform init \
    -backend-config="address=https://gitlab.com/api/v4/projects/11385199/terraform/state/live_infra" \
    -backend-config="lock_address=https://gitlab.com/api/v4/projects/11385199/terraform/state/live_infra/lock" \
    -backend-config="unlock_address=https://gitlab.com/api/v4/projects/11385199/terraform/state/live_infra/lock" \
    -backend-config="username=<Your Gitlab user name>" \
    -backend-config="password=$GITLAB_ACCESS_TOKEN" \
    -backend-config="lock_method=POST" \
    -backend-config="unlock_method=DELETE" \
    -backend-config="retry_wait_min=5"
```

>If the above `terraform init` command results in an error message like the following:
```
Initializing the backend...
Initializing modules...
╷
│ Error: Backend configuration changed
│ 
│ A change in the backend configuration has been detected, which may require migrating existing state.
│ 
│ If you wish to attempt automatic migration of the state, use "terraform init -migrate-state".
│ If you wish to store the current configuration with no changes to the state, use "terraform init -reconfigure".
╵
```

>Then try running this command:
```
$ export GITLAB_ACCESS_TOKEN=<GigaDB GITLAB ACCESS TOKEN>
terraform init -migrate-state\
    -backend-config="address=https://gitlab.com/api/v4/projects/11385199/terraform/state/live_infra" \
    -backend-config="lock_address=https://gitlab.com/api/v4/projects/11385199/terraform/state/live_infra/lock" \
    -backend-config="unlock_address=https://gitlab.com/api/v4/projects/11385199/terraform/state/live_infra/lock" \
    -backend-config="username=<Your Gitlab user name>" \
    -backend-config="password=$GITLAB_ACCESS_TOKEN" \
    -backend-config="lock_method=POST" \
    -backend-config="unlock_method=DELETE" \
    -backend-config="retry_wait_min=5"

Initializing the backend...
Backend configuration changed!

Terraform has detected that the configuration specified for the backend
has changed. Terraform will now check for existing state in the backends.

Successfully configured the backend "http"! Terraform will automatically
use this backend unless the backend configuration changes.
Initializing modules...

Initializing provider plugins...
- Reusing previous version of hashicorp/random from the dependency lock file
- Reusing previous version of hashicorp/external from the dependency lock file
- Reusing previous version of hashicorp/aws from the dependency lock file
- Using previously-installed hashicorp/random v3.1.3
- Using previously-installed hashicorp/external v2.2.2
- Using previously-installed hashicorp/aws v4.14.0

Terraform has been successfully initialized!
```
### What to do if Ansible playbook execution fails

1. Make sure `ansible` is installed,  and here is the [installation guide](https://docs.ansible.com/ansible/latest/installation_guide/index.html).
2. Make sure the third party Ansible roles are installed, run the following cmd to force overwriting an existing role if not sure:
```
$ ansible-galaxy install --force -r ../../../infrastructure/requirements.yml
```


### What if Ansible playbook cannot find host IP addresses

1. Make sure `terraform output` can produce bastion server IP and webapp server IP and the `terraform-inventory.sh` is working:
```
$ ../../inventories/terraform-inventory.sh --list
```
The terraform inventory script would look into the terraform state and should be able to output the resources information in a json format. 
2. Make sure the IPs have been added to the local `~/.ssh/known_hosts` file using:
```
$ ../../../scripts/ansible_init.sh --env live
```

### How to create a new user account in the bastion server

If a user wants to execute tools, eg. readme generate, excel spreadsheet uploader, etc in the bastion server, a user account with appropriate permission has to be created first.
The creation can be achieved as below:
```
% cd /gigadb-website/ops/infrastructure/envs/live
# make sure bastion server is up and running
% terraform output
# execute the user playbook to create a user account
% ansible-playbook -i ../../inventories users_playbook.yml -e "newuser=$user" --extra-vars="gigadb_env=live"
# check if bastion_public_key_$user (live) exists in the upstream gitlab variables page
# the private key will be saved as /output/privkeys-$bastion-ip/$user
# update the permission of the private key
% chmod 500 output/privkeys-$bastion-ip/$user
# connect to the bastion server
% ssh -i output/privkeys-$bastion-ip/$user $user@$bastion-ip
```

### What if a user has accidentally deleted (or corrupted) their .ssh/authorized_keys

Please contact tech team if a user suspects that the /home/$user/.ssh/authorized_keys has accidentally deleted or corrupted, tech team can help to retrieve it from the [upstream gitlab variable page](https://gitlab.com/gigascience/upstream/gigadb-website/-/settings/ci_cd) after logging in 
and put it back to the bastion server as /home/$user/.ssh/authorized_keys, while the authorized keys in gitlab would be in the form of bastion_public_key_$user. 

### What if a user has lost their private keys (or they are compromised)

Please contact tech team if a user has lost their private keys, tech team will first delete the existing /home/$user/.ssh/authorized_keys in the bastion server and also in the [upstream gitlab variable page](https://gitlab.com/gigascience/upstream/gigadb-website/-/settings/ci_cd), 
then execute the `user_playbook` for the user:
```
% ansible-playbook -i ../../inventories users_playbook.yml -e "newuser=$user" --extra-vars="gigadb_env=live"
```
which will generate a new pair of ssh keys, the new private key will then be sent to the user, while the public key will be pushed to the the gitlab variable page and also the bastion server.

### What if a user still cannot connect to the bastion server even they have a valid private key file in their computer and the corresponding public key in the bastion server

This may be because the sshd service in the bastion server has not been started properly, tech team will:
```
# login bastion server as a centos user
% ssh -i /path/to/id-rsa-aws-hk-gigadb.pem centos@$bastion-ip
# restart the sshd service
systemctl restart sshd.service
```

### How to convert ssh private key format from pem to ppk for a Window user

Mostly, Windows users will use PuTTY to connect with the remote server, and the connection requires a ssh private key in ppk format.
The format conversion can be done as below:
```
# install putty to make puttygen tool available on MacOS, if not
% brew install putty
# make sure puttygen is available
% puttygen --version
puttygen: Release 0.79
Build platform: 64-bit Unix
Compiler: clang 14.0.3 (clang-1403.0.22.14.1)
Source commit: b10059fc922aeb9343a55a409ea01740061d2440
# go to the location for storing the private key, for example
% cd gigadb-website/ops/infrastructure/envs/live
# make sure the private key is available
% ls -l output/privkeys-$bastion-ip/
total 8
-r-x------@ 1 kencho  staff  3357 Nov 29 13:58 $user
# convert the format from pem to ppk
% puttygen $user -o $user.ppk
 % ls -l output/privkeys-$bastion-ip/
total 16
-r-x------@ 1 kencho  staff  3357 Nov 29 13:58 $user
-rw-------@ 1 kencho  staff  2659 Nov 30 13:06 $user.ppk
```

Then the $user.ppk can be sent to the user for the server connection. 

### How to connect to the bastion server for a Windows user

Assuming PuTTY is installed by the user, details can be found at [here](https://www.chiark.greenend.org.uk/~sgtatham/putty/latest.html), and the user has received the private key from the tech team.

Then user can connect to the bastion server with the given private key as below:
```
1. Open PuTTY programme
2. Enter the remote server Host Name or IP address under "Session".
3. Navigate to "Connection" > "SSH" > "Auth".
4. Click "Browse..." under "Authentication parameters" / "Private key file for authentication".
5. Locate the $user.ppk private key and click "Open".
6. Finally, click "Open" again to log into the remote server with key pair authentication.
```
