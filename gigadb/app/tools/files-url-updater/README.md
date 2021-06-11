# Tools for GigaDB: files-url-updater


## Start the local test database

```
$ docker-compose up -d pg9_3
```
## Run test

```
$ docker-compose run --rm updater ./vendor/bin/codecept build
$ docker-compose run --rm updater ./vendor/bin/codecept run -v

```

## run specific tests

```
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/functional/DatasetFilesCest.php
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/functional -v
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/acceptance -v
```


## Run commands

```
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210530
$ docker-compose run --rm updater ./yii dataset-files/update-ftp-url --ids 12,34,67
```

## Get help

```
$ docker-compose run --rm updater ./yii
$ docker-compose run --rm updater ./yii help dataset-files
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --help

```

## Adding new acceptance tests

1. add ``*.feature`` files to ``tests/acceptance``
2. Run:
```
  $ docker-compose run --rm updater ./vendor/bin/codecept gherkin:snippets acceptance
```
3. Implement steps in ``tests/_support/AcceptanceTester.php``

## Update manually test database schema

use the ``download-restore-backup`` command to load the main database, then dump the content into the ``gigadb_tables.sql`` file.
```
$ docker-compose run --rm updater pg_dump --schema-only --verbose -h pg9_3 -U gigadb gigadb -f sql/gigadb_tables.sql
```

Then load it in the test database
```
$ docker-compose run --rm updater psql -h pg9_3 -U gigadb --dbname gigadb_test -f sql/gigadb_tables.sql
```

This is done automatically in functional tests setup

## Gotchas for restoring the production backup using default user

Remove any instructions from the binary dump about loading extensions as that require superuser
privileges

```
$ docker-compose run --rm updater bash -c "pg_restore -l sql/gigadbv3_20210608.backup | grep -v 'COMMENT - EXTENSION' | grep -v 'plpgsql' > sql/pg_restore.list"
```

And then pass the generated list of archive items to include in the restoration to the ``pg_restore`` command:

```
$ docker-compose run --rm updater bash -c "pg_restore --exit-on-error --verbose --use-list sql/pg_restore.list -h pg9_3 -U gigadb --dbname gigadb  sql/gigadbv3_20210608.backup"
```
