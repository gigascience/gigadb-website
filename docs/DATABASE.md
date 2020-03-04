# Database setup

The local dev and production deployments of the GigaDB application have 
discrepancies in their PostgreSQL database schemas. These differences need to be
removed for development work. Rija suggested using Yii database migrations for
instantiating a dev PostgreSQL database.

## Notes

### Migration script creation

```
# Log into test container
docker-compose run --rm test bash
$ Change directory
cd /var/www/protected
# Create migration
./yiic migrate create create_dataset_table
```

N.B. The migration PHP files will use tab, and not space characters for indents.

### Redundant files

* The ops/configuration/postgresql-conf directory contains a SQL file 
`empty_bootstrap.sql` for creating an empty gigadb database that contains no 
tables.

* The protected/migrations directory contains 4 migrations:

1. m181108_105104_widen_user_password_column.php
2. m181128_090415_setup_curation_log_sequence.php
3. m190527_025615_dataset_upload_status.php
4. m191015_013705_alter_curation_comments.php

These PHP files should be deleted since they will all be redundant with this
new database setup.

### Data privacy

Need to be aware of privacy of email addresses in test data, e.g. gigadb_user table.




