# SOP: Restoring a database backup in S3 for beta.gigadb.org

Database dump files are generated on a daily basis if the cron job for the
`restore_database_from_s3_backup.sh` has been created. These database dump files
stored in S3 and can be used to restore the database on RDS to a previous state.

## Prerequisites

### Access to AWS S3 API

Accessing the S3 bucket containing the database backup files requires AWS
credentials. These are provided as Gitlab variables in the
`Upstream/gigadb-website` project set for `all` environment:

* AWS_ACCESS_KEY_ID
* AWS_SECRET_ACCESS_KEY

These variables are provisioned by Ansible for the configuration of rclone by
the bastion playbook.

## Procedure

Change directory to `/path/to/gigascience/gigadb-website/ops/infrastructure/envs/live`
and log into the bastion server. You can find its address from the AWS console when 
logged in as the `Gigadb` IAM user. You can also get the bastion server public IP 
address this way:
```
$ terraform output
ec2_bastion_private_ip = "10.xx.x.xxx"
ec2_bastion_public_ip = "16.xxx.xxx.xxx"
ec2_private_ip = "10.xx.x.xxx"
ec2_public_ip = "16.xxx.xxx.xx"
rds_instance_address = "rds-server-live-gigadb.xxxxxxxxxxxx.ap-east-1.rds.amazonaws.com"
vpc_database_subnet_group = "vpc-ap-east-1-live-gigadb-gigadb"
```

If you are not the developer that instantiated a current running GigaDB application
then the `live` directory will be empty and no outputs will be displayed:
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

You should now have been able to successfully execute `terraform output` which will then display the bastion server IP
address that can then be used to log into the server:
```
$ ssh -i ~/.ssh/id-rsa-aws-hk-gigadb.pem centos@<bastion public ip>
```

Execute this command to restore the database from a backup taken on YYYYMMDD
```
$ docker run --rm --env-file .env -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf --entrypoint /restore_database_from_s3_backup.sh registry.gitlab.com/gigascience/upstream/gigadb-website/production_s3backup:live YYYYMMDD
```

Check https://beta.gigadb.org and look at the RSS feed. Does it contain the feed
contain the expected list of datasets?