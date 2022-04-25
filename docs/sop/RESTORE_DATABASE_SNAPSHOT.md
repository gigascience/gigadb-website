# SOP: Restoring a database snapshot for beta.gigadb.org

We use the Amazon RDS to provide us with a PostgreSQL RDBMS which is used to
host a database that contains GigaDB's metadata about datasets. In constrast to
database backups, snapshots are saved storage volume of a database instance 
which are created by users. Snapshots also do not expire in contrast to 
automated database backups.

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

## Use snapshot to restore PostgreSQL RDBMS

Go to environment directory:
```
$ cd <path_to>/PhpstormProjects/gigascience/gigadb-website/ops/infrastructure/envs/staging
```

Terminate existing RDS service:
```
$ terraform destroy --target module.rds
```

Restore database snapshot:
```
$ terraform plan -var snapshot_identifier="snapshot-for-testing"
$ terraform apply -var snapshot_identifier="snapshot-for-testing"
$ terraform refresh
```
