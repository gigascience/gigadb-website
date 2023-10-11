# Troubleshooting guide for the GigaDB Website deployment

This page provides solution for the problems when deploying the production GigaDB Website.


### How to login the webapp server visa ssh

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

The details of TLS setup can refer to the [TLS doc](../TLS.md). Currently, the Let's Encrypt's certificate only last for 90 days, and Let's Encrypt's will start sending out reminder emails of renewing 30 days before expiration.

```
# login webapp server
$ ssh -i path/to/id-rsa-aws-hk-gigadb.pem -o ProxyCommand='ssh -i ~path/to/id-rsa-aws-hk-gigadb.pem -W %h:%p centos@$ec2_bastion_public_ip' centos@$ec2_private_ip
$ docker ps -a
CONTAINER ID   IMAGE                                                                         COMMAND                  CREATED       STATUS       PORTS                                                                                            NAMES
de1945448b44   portainer/portainer-ce:latest                                                 "/portainer -H unix:…"   13 days ago   Up 13 days   0.0.0.0:8000->8000/tcp, :::8000->8000/tcp, 9443/tcp, 0.0.0.0:9009->9000/tcp, :::9009->9000/tcp   gigadb-website_portainer_1
95b70997ce2a   registry.gitlab.com/gigascience/upstream/gigadb-website/production_web:live   "/docker-entrypoint.…"   13 days ago   Up 13 days   0.0.0.0:80->80/tcp, :::80->80/tcp, 0.0.0.0:443->443/tcp, :::443->443/tcp                         gigadb-website_web_1
d55b8bc1d82f   registry.gitlab.com/gigascience/upstream/gigadb-website/production_app:live   "docker-php-entrypoi…"   13 days ago   Up 13 days   9000/tcp, 9135/tcp                        
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
drwxr-xr-x    2 root     root            93 Sep 26 16:04 beta.gigadb.org
$ docker exec gigadb-website_web_1 ls -1l /etc/letsencrypt/live/beta.gigadb.org
total 4
-rw-r--r--    1 root     root           692 Sep 26 16:04 README
lrwxrwxrwx    1 root     root            39 Sep 26 16:04 cert.pem -> ../../archive/beta.gigadb.org/cert1.pem
lrwxrwxrwx    1 root     root            40 Sep 26 16:04 chain.pem -> ../../archive/beta.gigadb.org/chain1.pem
lrwxrwxrwx    1 root     root            44 Sep 26 16:04 fullchain.pem -> ../../archive/beta.gigadb.org/fullchain1.pem
lrwxrwxrwx    1 root     root            42 Sep 26 16:04 privkey.pem -> ../../archive/beta.gigadb.org/privkey1.pem
$ docker volume ls
DRIVER    VOLUME NAME
local     gigadb-website_assets
local     gigadb-website_feeds
local     gigadb-website_le_config
local     gigadb-website_le_webrootpath
local     gigadb-website_portainer_data
# stop and remove all running containers
$ docker rm -f $(docker ps -a -q)
# remove all exiting volumes
$ docker volume rm $(docker volume ls -q)
```

Then delete the existing certs `tls_chain_pem:live`, `tls_fullchain_pem:live` and `tls_privkey_pem:live` in the gitlab [upstream variable page](https://gitlab.com/gigascience/upstream/gigadb-website/-/settings/ci_cd).
After that, manually start the `ld_gigadb` in the last successful pipeline in gitlab and you will see screen output likes this:
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

### What if Ansible playbook cannot find host IP addresses

