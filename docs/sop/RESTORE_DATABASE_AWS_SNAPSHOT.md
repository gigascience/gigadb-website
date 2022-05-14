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
2. Click on the [Snapshots](https://ap-east-1.console.aws.amazon.com/rds/home?region=ap-east-1#snapshots-list:) link located on the left hand side menu in
   the dashboard.
3. In the `Manual` tab, decide which snapshot you want to restore by clicking on
   its checkbox
4. Click the `Actions` button and select the `Restore snapshot` option
5. In the *Settings* box, provide a DB instance identifier such as `rds-server-live-gigadb`
6. In the *Connectivity* box, select the required VPC based on `live`
   environment, i.e. `vpc-ap-east-1-live-gigadb-gigadb`
7. In the *Connectivity* box, `Public access` should be *No*
8. In the *Connectivity* box, select VPC security group, e.g. `rds_sg_live_gigadb-20220426875429729`
9. In the *Instance configuration* box, select `burstable classes` then select
   the cheapest instance type `db.t3.micro`
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
    instance being created under the *Status* column - started 8.10 pm
17. When the RDS instance has been created, check if you need to manually add
    its tags:
* Environment = live
* Owner = gigadb

## Using the command-line to restore an automated backup onto a new RDS instance

### Prerequisites

If we want to restore a database snapshot onto a new RDS instance with the same 
DB instance identifier, e.g. `rds-server-staging-gigadb` or 
`rds-server-live-gigadb` then any pre-existing RDS instances with these 
identifiers need to be deleted first.

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

1. We need to decide which snapshot to restore. RDS instances can create 
two types of snapshots. `System snapshots` are created automatically and 
retained for a limited period based on the backup retention time. `Manual snapshots`
are created via the RDS console and AWS CLI, and are retained indefinitely.
To get a list of snapshots, execute:
```
$ aws rds describe-db-snapshots
```
The key information is the value of the `DBSnapshotIdentifier` key for the 
snapshot we want to use. There is also a "TagList" key that show that the 
important tags are preserved.
2. Go to environment directory:
```
$ cd <path_to>/PhpstormProjects/gigascience/gigadb-website/ops/infrastructure/envs/staging
```
5. Terminate existing RDS service:
```
$ terraform destroy --target module.rds
```
6. Restore database snapshot using the DB snapshot name selected from Step 1:
> :warning: **If you have an `override.tf` file in your environment directory, this needs to be removed**
```
# Use DB snapshot bame as the value for snapshot_identifier
$ terraform plan -var snapshot_identifier="snapshot-final-staging-gigadb-20220428042656-rds-server-staging-gigadb-7f221735"
$ terraform apply -var snapshot_identifier="snapshot-final-staging-gigadb-20220428042656-rds-server-staging-gigadb-7f221735"
```
7. Browse staging.gigadb.org in web browser to check GigaDB website is running