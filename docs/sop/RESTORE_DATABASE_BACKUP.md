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
`rds-server-staging-peter`. This instance identifier is used in its endpoint, 
for example, `rds-server-staging-peter.c6rywcayjkwa.ap-northeast-1.rds.amazonaws.com`.
This endpoint is used in any configuration used to access the database by an 
application. 

If the identifier for a RDS instance is consistently used then we can assume 
that an RDS instance endpoint will not change for the PostgreSQL RDBMS for the
staging.gigadb.org or beta.gigadb.org. This means we can swap in new RDS 
instances that have been restored from automated backups, snapshots and database
dump files without compromising connectivity of the database with the web 
application.

## Using the AWS RDS dashboard to restore an automated backup on a new RDS instance

### Prerequisites

If we want to restore a database backup onto a new RDS instance with the same DB 
instance identifier, e.g. `rds-server-staging-gigadb` or `rds-server-live-gigadb`
then any pre-existing RDS instances with these identifiers need to be deleted 
first.

### Procedure

1. Go to the AWS [RDS Dashboard](https://ap-east-1.console.aws.amazon.com/rds/home?region=ap-east-1#) 
for the Hong Kong ap-east-1 region.
2. Click on the [Automated backups](https://ap-east-1.console.aws.amazon.com/rds/home?region=ap-east-1#automatedbackups:) link located on the left hand side menu in 
the dashboard. 
3. In the `Retained` tab, decide which backup you want to restore by clicking on
its radio button
4. Click the `Actions` button and select the `Restore to point in time` option
5. In the *Restore time* box, decide whether you want to the `Latest restorable time`
or a `Custom data and time`
6. In the *Settings* box, enter `rds-server-live-gigadb` as the database 
instance identifier. You will be able to do this because you will have already 
deleted the RDS instance that had this database instance identifier
7. In the *Instance configuration~ box, select `burstable classes` then select 
the cheapest instance type `db.t3.micro`
8. In the *Connectivity* box, select the required VPC based on `live` 
environment, i.e. `vpc-ap-east-1-live-gigadb-gigadb`
9. In the *Connectivity* box, `Public access` should be *No*
10. In the *Connectivity* box, select VPC security group, e.g. `rds_sg_live_gigadb-20220426875429729`
11. In the *Connectivity* box, *Password authentication* should be deleted
12. In the *Additional configuration* box, there is no need to provide an `initial database name`
because the automated backup contains the names of the databases to be restored
13. Check the `Copy tags to snapshots` checkbox
14. Do not check `Enable auto minor version upgrade` checkbox
15. Do not check `Enable deletion protection` checkbox
16. Click `Restore to point in time` button
17. When the RDS instance has been created then you will need to manually add 
its tags:
* Environment = live
* Owner = gigadb

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
