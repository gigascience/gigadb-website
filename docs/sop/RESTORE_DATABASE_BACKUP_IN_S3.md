# SOP: Restoring a database backup in S3 for beta.gigadb.org

Database dump files are generated on a daily basis if the cron job for the
`restore_database_from_s3_backup.sh` has been created. These database dump files
stored in S3 and can be used to restore the database on RDS to a previous state.

## Procedure

Log into the bastion server. You can find its address from the AWS console
when logged in as the `Gigadb` IAM user.
```
# Alternative way to get public ip of bastion server
$ terraform output

# ssh into it
$ ssh -i ~/.ssh/id-rsa-aws-hk-gigadb.pem centos@<bastion public ip>
```

Change directory:
```
$ cd /home/centos/files-url-updater
```

Run shell script:
```
$ ./restore_database_from_s3_backup.sh
```

Check https://beta.gigadb.org and look at the RSS feed. Does it contain the feed
contain the expected list of datasets?