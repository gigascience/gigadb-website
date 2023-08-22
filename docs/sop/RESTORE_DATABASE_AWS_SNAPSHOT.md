# SOP: Restoring a database snapshot for beta.gigadb.org

Snapshots are user-initiated backups of an RDS instance. Like automated backups,
these snapshots can be restored into a new database instance. These snapshots
are stored in S3 but this is opaque to users. Snapshots are specific to an AWS
region but they can be copied into another AWS region. Snapshots are saved 
storage volume of a database instance and do not expire in contrast to automated 
database backups.

## Using the AWS RDS dashboard to restore a manual snapshot onto a new RDS instance

### Prerequisites

If we want to restore a database backup onto a new RDS instance with the same DB
instance identifier, e.g. `rds-server-staging-gigadb` or `rds-server-live-gigadb`
then any pre-existing RDS instances with these identifiers need to be deleted
first.

This can be done by going to the `Databases` page on the AWS RDS console, 
clicking the radio button of the database identifier for the instance that needs
to be deleted. Then click the `Actions` button and selecting `Delete` option.

A popup window will ask if you want create a final snapshot and retain automated
backups. Check both these options and click `Delete`.

### Procedure

1. Go to the AWS [RDS Dashboard](https://ap-east-1.console.aws.amazon.com/rds/home?region=ap-east-1#)
   for the Hong Kong ap-east-1 region.
2. Click on the [Snapshots](https://ap-east-1.console.aws.amazon.com/rds/home?region=ap-east-1#snapshots-list:)
   link located on the left hand side menu in the dashboard.
3. In the `Manual` tab, decide which snapshot you want to restore by clicking on
   its checkbox
4. Click the `Actions` button and select the `Restore snapshot` option
5. In the *Settings* box, provide a DB instance identifier such as `rds-server-live-gigadb`
6. In the *Instance configuration* box, select `burstable classes` then select
   the cheapest instance type `db.t3.micro`
7. In the *Connectivity* box, select the required VPC based on `live`
   environment, i.e. `vpc-ap-east-1-live-gigadb-gigadb`
8. In the *Connectivity* box, `Public access` should be *No*
9. In the *Connectivity* box, select VPC security group, e.g. `rds_sg_live_gigadb-20220426875429729`
10. In the *Database authentication* box, *Password authentication* should be 
    selected
11. In the *Additional configuration* box under the *DB parameter group* heading,
    select `gigadb-db-param-gigadb`
12. In the *Additional configuration* box under the *Backup* heading, check 
    *Copy tags to snapshots*
13. Do not check `Enable auto minor version upgrade` checkbox
14. Do not check `Enable deletion protection` checkbox
15. Click `Restore DB instance` button
16. You will be taken to the `Databases` page where you will see the new 
    instance being created under the *Status* column.
17. When the RDS instance has been created, check if you need to manually add
    its tags:
* Environment = live
* Owner = gigadb
18. Browse staging.gigadb.org in web browser to check GigaDB website is running
