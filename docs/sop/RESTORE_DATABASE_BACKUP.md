# SOP: Restoring a database backup for beta.gigadb.org

We use the Amazon RDS to provide us with a PostgreSQL RDBMS for hosting a 
database that contains GigaDB's metadata about datasets. The RDS automatocally
creates backups of the PostgreSQL database instance during a backup window 
configured in `rds-instance.tf`. Currently, backups are created 03:00-06:00 UTC 
time with each backup retained for 5 days. The database can be recovered to any 
point during the retention period.

Automated backups are not deleted when `terraform destroy` is executed. However,
we get charged for these backups at a price of $0.095 per GB-month.

RDS instances can be identified by their RDS instance identifier, e.g. 
`rds-server-staging-gigadb`. Their endpoint, e.g. 
`rds-server-staging-peter.c6rywcayjkwa.ap-northeast-1.rds.amazonaws.com` is a 
kind of identifier but this does not appear to change. Which means that we can
swap in new RDS instances without compromising connectivity of the database 
with the web application.

## Using the AWS RDS dashboard to restore an automated backup on a new RDS instance

### Prerequisites

If we want to restore a backup onto a new RDS instance with the same DB instance
identifier then any RDS instance with this identifier needs to be deleted first.

### Procedure

1. Go to https://ap-northeast-1.console.aws.amazon.com/rds/home?region=ap-east-1#automatedbackup-pitr:id=rds-server-staging-gigadb;restore=full-copy
2. Restore time: Select `Latest restorable time`
3. Settings: Provide a unique DB instance identifier. These identifiers are
   named with this format: `rds-server-staging-gigadb` or `rds-server-live-gigadb`.
   This means that you will need to delete the current RDS instance then re-create
   it from an automated backup if we want to use the same structure.
4. Instance configuration - Select burstable classes and select cheapest
   instance type `db.t3.micro`
5. Connectivity - select required VPC based on environment, e.g. `vpc-ap-east-1-live-gigadb-peter`
6. Connectivity - select VPC security group, e.g. `rds_sg_live_gigadb-20220426875429729`
7. Public access - no
8. Password authentication
9. No need to provide initial database name because the automated backup
   contains the databases
10. Copy tags to snapshots
11. Do not enable auto minor version upgrade
12. No need to enable deletion protection
13. Each RDS instance has an endpoint domain name that is used to access it, e.g
    `rds-server-staging-peter.c6rywcayjkwa.ap-northeast-1.rds.amazonaws.com`. This
    same endpoint domain name is allocated to the newly restored RDS instance:
    `rds-server-staging-peter.c6rywcayjkwa.ap-northeast-1.rds.amazonaws.com`.
14. Click `Restore to poiint in time` button
15. Need to manually add tags as these are missing on the new restored RDS 
instance. `TODO`: what tags does the new restored DB instance need? 

## Prerequisites

### Update AWS credentials configuration

1. Check `id-rsa-aws-hk-gigadb.pem` available from [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd)
is in  your `~/.ssh` directory.

2. You should have `config.upstream` and `credentials.upstream` in your 
`~/.aws` directory. These files should be used to update the actual `config`
and `credentials` files in `~/.aws`:
```
$ cp config.upstream config
$ cp credentials.upstream credentials
```
> :warning: **You will need to overwrite the upstream `config` and `credentials` files with `config.ap-northeast-1` and `credentials.ap-northeast-1` when returning to your development work**

### Command-line instructions

Go to environment directory:
```
$ cd <path_to>/PhpstormProjects/gigascience/gigadb-website/ops/infrastructure/envs/staging
```

Terminate existing RDS service:
```
$ terraform destroy --target module.rds
```

Copy `override.tf` to staging environment:
```
$ ../../../scripts/tf_init.sh --project gigascience/forks/pli888-gigadb-website --env staging --restore-backup
```

The PostgreSQL RDBMS can either be restored to its latest restorable time or to 
a specific time using RDS backups. To restore to latest restorable time, we need
to override the database name since this will come from the backup:
```
# Get list of dbis
$ aws rds describe-db-instance-automated-backups

$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var use_latest_restorable_time="true"
```

To restore to specific time in backup - need to override database name as this 
will come from the backup:
```
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var utc_restore_time="2021-10-27T06:02:12+00:00"
```
