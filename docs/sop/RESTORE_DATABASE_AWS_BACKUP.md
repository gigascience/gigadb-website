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
8. In the *Instance configuration* box, select `burstable classes` then select 
the cheapest instance type `db.t3.micro`, then select `Single DB instance` in the *Availability and durability* box beneath.
9. In the *Connectivity* box, select the required VPC based on `live` 
environment, i.e. `vpc-ap-east-1-live-gigadb-gigadb`
10. In the *Connectivity* box, `Public access` should be *No*
11. In the *Connectivity* box, select VPC security group, e.g. `rds_sg_live_gigadb-*`
12. In the *Database authentication* box, *Password authentication* should be selected
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
