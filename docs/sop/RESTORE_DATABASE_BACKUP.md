# SOP: Restoring a database backup for beta.gigadb.org

We use the Amazon RDS to provide us with a PostgreSQL RDBMS which is used to 
host a database that contains GigaDB's metadata about datasets. The RDS creates
and saves automated backups of the PostgreSQL database instance during a 
backup window configured in `rds-instance.tf`. Currently, backups are created
03:00-06:00 UTC time with each backup retained for 5 days. The database can be
recovered to any point during the retention period.

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

## Restore PostgreSQL RDBMS to the latest restorable time

Go to environment directory:
```
$ cd <path_to>/PhpstormProjects/gigascience/gigadb-website/ops/infrastructure/envs/staging
```

Terminate existing RDS service:
```
$ terraform destroy --target module.rds
```

Copy override.tf to staging environment:
```
$ ../../../scripts/tf_init.sh --project gigascience/forks/pli888-gigadb-website --env staging --restore-backup
```

Backups can either be restored to its latest restorable time or to a specific
time. To restore to latest restorable time, we need to override the database 
name since this will come from the backup:
```
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var use_latest_restorable_time="true"
```

To restore to specific time in backup - need to override database name as this 
will come from the backup:
```
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var utc_restore_time="2021-10-27T06:02:12+00:00"
```
