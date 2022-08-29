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

Log into the bastion server. You can find its address from the AWS console
when logged in as the `Gigadb` IAM user.
```
# Alternative way to get public ip of bastion server
$ terraform output

# ssh into it
$ ssh -i ~/.ssh/id-rsa-aws-hk-gigadb.pem centos@<bastion public ip>
```

Execute the command to restore the database from a backup taken on YYYYMMDD
```
$ docker run --rm --env-file .env -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf --entrypoint /restore_database_from_s3_backup.sh  registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_s3backup:staging YYYYMMDD
```

Check https://beta.gigadb.org and look at the RSS feed. Does it contain the feed
contain the expected list of datasets?