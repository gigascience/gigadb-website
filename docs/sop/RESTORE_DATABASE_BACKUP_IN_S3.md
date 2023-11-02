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

If there is any terraform problems, please look for the fix at [here](PRODUCTION_TROUBLESHOOT.md#what-to-do-if-terraform-execution-fails)

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