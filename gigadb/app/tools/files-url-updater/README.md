# Tools for GigaDB: files-url-updater


## working directory for this tool

```
$  cd gigadb/app/tools/files-url-updater/ 
```

## Install Composer dependencies

```
$  composer install 
```

## Start the local database that mimics production

```
$ docker-compose up -d pg9_3
```

## Configuring access to the database

```
$ cp config/params.php.example config/params.php
```

The above is enough. Specifying the DB password is not necessary for running the command line tool
against the local database spun up above.
It is not necessary for running the tests either.

However, It is required to specify appropriate database name, username and password 
if you want to use the tool to use the tool on a remote database

## Populate the local database with a copy of production database backup

```
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210608 --nodownload
```

If you need a backup for a specific date you can remove the ``--nodownlaod`` option and 
specify a date with the last seven days to the ``--date`` parameter.
Omitting the ``--date`` parameter altogether will download the latest backup (the one from the day before)

However, before the tool can download a backup of production database, you need to ensure
the ``host``, ``username``, and ``password`` keys of the ``ftp`` section of ``config/params.php``
are appropriately specified.


## configure the main GigaDB app to talk to the legacy database

This is needed for running the acceptance tests.

Update the Database configuration file ``../../../../protected/config/db.json``
to read as below:

```
{
    "database": "gigadb",
    "host"    : "host.docker.internal",
    "user"    : "gigadb",
    "password": "",
}

```

## Run test

```
$ docker-compose run --rm updater ./vendor/bin/codecept build
$ docker-compose run --rm updater ./vendor/bin/codecept run -v

```

## run specific tests

```
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/units
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/functional
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/acceptance
```


## running the tool

```
 $ docker-compose run --rm updater ./yii dataset-files/update-ftp-urls --next 10 --after 30 --verbose
```

use ``--help`` to get an explanation of all the options


