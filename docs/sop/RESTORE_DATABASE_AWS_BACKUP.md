# SOP: Restoring a database backup for beta.gigadb.org

Amazon's RDS provides a PostgreSQL RDBMS for hosting a database that contains 
GigaDB's metadata about datasets. The RDS automatically creates backups of the 
PostgreSQL database instance during a backup window configured in 
`rds-instance.tf`. Currently, backups are created between 03:00-06:00 UTC time 
with each backup retained for 5 days. The database can be restored to any 
point during the 5 day backup retention period.

Automated backups are not deleted when `terraform destroy` is executed. However,
we get charged for these backups at a price of $0.095 per GB-month.

> :warning: **We need to be mindful about deleting these backups created as part of development work**

RDS instances are identified by their RDS instance identifier, e.g. 
`rds-server-live-gigadb`. This instance identifier is used in its endpoint, 
for example, `rds-server-live-gigadb.c6rywcayjkwa.ap-east-1.rds.amazonaws.com`.
This endpoint is used in any configuration used to access the database by an 
application. 

If the identifier for a RDS instance is consistently used then we can assume 
that an RDS instance endpoint will not change for the PostgreSQL RDBMS for the
staging.gigadb.org or beta.gigadb.org. This means we can swap in new RDS 
instances that have been restored from automated backups, snapshots and database
dump files without compromising connectivity of the database with the web 
application.

## Using the AWS RDS dashboard to restore an automated backup onto a new RDS instance

### Procedure

> :warning: **Restoring an RDS instance from an automated backup will take several minutes. The GigaDB website will also show a `504 gateway timeout` message until the new DB instance is ready**

1. Log into the AWS console using the `Gigadb` AWS user account. Select the
[RDS Dashboard](https://ap-east-1.console.aws.amazon.com/rds/home?region=ap-east-1#)
for the Hong Kong ap-east-1 region.
2. If we want to restore a database backup onto a new RDS instance with the same 
DB instance identifier, e.g. `rds-server-staging-gigadb` or `rds-server-live-gigadb`
then any pre-existing RDS instances with these identifiers need to be deleted
first. This can be done from the [RDS Dashboard](https://ap-east-1.console.aws.amazon.com/rds/home?region=ap-east-1#).
Also, make sure to check the `Retain automated backups` checkbox, otherwise there
will not be any retained backup to work with the rest of this procedure. Deletion
of the RDS instance will take several minutes.
3. Click on the [Automated backups](https://ap-east-1.console.aws.amazon.com/rds/home?region=ap-east-1#automatedbackups:)
link located on the left-hand side menu in the dashboard. 
4. In the `Retained` tab, decide which backup you want to restore by clicking on
its radio button
5. Click the `Actions` button and select the `Restore to point in time` option
6. In the *Restore time* box, decide whether you want to the `Latest restorable time`
or a `Custom data and time`
7. In the *Settings* box, enter `rds-server-live-gigadb` as the database 
instance identifier. You will be able to do this because you will have already 
deleted the RDS instance that had this database instance identifier
8. In the *Instance configuration~ box, select `burstable classes` then select 
the cheapest instance type `db.t3.micro`
9. In the *Connectivity* box, select the required VPC based on `live` 
environment, i.e. `vpc-ap-east-1-live-gigadb-gigadb`
10. In the *Connectivity* box, `Public access` should be *No*
11. In the *Connectivity* box, select VPC security group, e.g. `rds_sg_live_gigadb-*`
12. In the *Connectivity* box, *Password authentication* should be deleted
13. In the *Additional configuration* box, there is no need to provide an `initial database name`
because the automated backup contains the names of the databases to be restored.
You should make sure that the appropriate DB parameter group is selected. For
the `Gigadb` user, this will be `gigadb-db-param-group-gigadb`.
14. Check the `Copy tags to snapshots` checkbox
15. Do not check `Enable auto minor version upgrade` checkbox
16. Do not check `Enable deletion protection` checkbox
17. Click `Restore to point in time` button. This will lead you back to the
`Databases` RDS console page where it will show a `Creating` status for your 
RDS instance.
18. When the RDS instance has status `Available` then you will need to manually add 
its tags:
* Environment = live
* Owner = gigadb

This is because restored RDS instances do not have any tags. If the above tags
are not present then you will not be able to destroy the instance because IAM
policy requires resources to be tagged with Owner in order to do this.

## Using the command-line to restore an automated backup onto a new RDS instance 

### Update AWS credentials configuration

1. Check that `id-rsa-aws-hk-gigadb.pem` from [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd)
is in your `~/.ssh` directory.

2. You should have `config.upstream` and `credentials.upstream` in your 
`~/.aws` directory. These files should be used to update the actual `config`
and `credentials` files in `~/.aws`:
```
# Contents of upstream config and credentials 
$ more config.upstream 
[default]
region=ap-east-1
output=json

[profile Gigadb]
region=ap-east-1
output=json

$ more credentials.upstream 
[default]
aws_access_key_id=AAAAAAAAAAAAAAAAAA
aws_secret_access_key=ZZZZZZZZZZZZZZZZZZZZ

[Gigadb]
aws_access_key_id=AAAAAAAAAAAAAAAAAA
aws_secret_access_key=ZZZZZZZZZZZZZZZZZZZZ


$ cp config.upstream config
$ cp credentials.upstream credentials
```
> :warning: **You will need to overwrite the upstream `config` and `credentials` files with `config.ap-northeast-1` and `credentials.ap-northeast-1` when returning to your development work**

### Command-line instructions

Go to environment directory:
```
$ cd /path/to/gigascience/gigadb-website/ops/infrastructure/envs/live
```

If you are not the developer that instantiated a current, running GigaDB
application then the `live` directory will be empty and no outputs will be 
displayed:
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

If we want to restore a database backup onto a new RDS instance with the same DB
instance identifier, e.g. `rds-server-staging-gigadb` or `rds-server-live-gigadb`
then any pre-existing RDS instances with these identifiers need to be deleted
first. Therefore, terminate existing RDS service:
```
$ terraform destroy --target module.rds
```

Copy `override.tf` to the staging environment directory:
```
$ ../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env staging --restore-backup
```

The PostgreSQL RDBMS can either be restored to the latest restorable time or to 
a specific time using an RDS backup. In both cases, the database name has to be
overridden since this will come from the backup.

Database backups are identified by their dbi_resource_id which needs to be
specified in the Terraform restoration command. To get a list of 
dbi_resource_ids, execute:
```
# Look for the DbiResourceId property
$ aws rds describe-db-instance-automated-backups
{
    "DBInstanceAutomatedBackups": [
        {
            "DBInstanceArn": "arn:aws:rds:ap-east-1:123456789101:db:rds-server-staging-peter",
            "DbiResourceId": "db-123456789ABCDEFGHIJKLMNOPQ",
            "Region": "apne-1",
            "DBInstanceIdentifier": "rds-server-staging-gigadb",
            "RestoreWindow": {
                "EarliestTime": "2022-04-26T08:27:35.984Z",
                "LatestTime": "2022-04-27T09:34:36Z"
            },
            "AllocatedStorage": 8,
            "Status": "retained",
            "Port": 5432,
            "AvailabilityZone": "ap-east-1c",
            "VpcId": "vpc-09e15bc6ec7eda888",
            "InstanceCreateTime": "2022-04-26T08:17:06Z",
            "MasterUsername": "gigadb",
            "Engine": "postgres",
            "EngineVersion": "11.13",
            "LicenseModel": "postgresql-license",
            "OptionGroupName": "default:postgres-11",
            "Encrypted": true,
            "StorageType": "gp2",
            "KmsKeyId": "arn:aws:kms:ap-east-1:123456789888:key/0ca5a6a0-9216-4acd-9fa1-43a4c7698fe7",
            "IAMDatabaseAuthenticationEnabled": false,
            "DBInstanceAutomatedBackupsArn": "arn:aws:rds:ap-northeast-1:123456789101:auto-backup:ab-5cmixz7ezjfj4wnj7mrf4vg4dbybg3adf2anppp"
        }
    ]
}
```

Once a `dbi_resource_id` has been selected you can restore to the latest 
restorable time using this command:
```
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQZON" -var gigadb_db_database="" -var use_latest_restorable_time="true"
```

To restore to specific time, you will need to determine the time to restore to:
```
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQZON" -var gigadb_db_database="" -var utc_restore_time="2021-10-27T06:02:12+00:00"
```

With both the above terraform commands, check that `Environment` and `Owner` 
tags exist for the new RDS instance. Create these tags if they are not present.
