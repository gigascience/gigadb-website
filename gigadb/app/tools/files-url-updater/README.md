# Tools for GigaDB: files-url-updater

## Introduction

This project offers two tools implemented as Yii2 Console commands:

* ``dataset-files/download-restore-backup``: restore the configured database server with a production backup of a specified date
* ``dataset-files/update-ftp-urls``: In the configured database, replace ftp urls to CNGB endpoints with https urls

The first one was implemented primarily to help testing the main tool ``dataset-files/update-ftp-urls``.
However, it could also be used as an operational tool to restore from a backup the production database server
whose data has been corrupted or lost.

## Working directory for this tool

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

This is needed by the automated tests and if the user wants to explore locally
the content of a production database backup.

## Configuring access to the database

```
$ cp config/params.php.example config/params.php
```

Specifying the DB password is not necessary for running the command line tool
against the local database spun up above.
It is not necessary for running the tests either.

However, It is required to specify appropriate database name, username and password 
if you want to use the tool on a remote database (e.g: the production database server)

Here are the meaning of the keys in the config file:
```
<?php

return [
    'db' => [ # connection details to the database setup we wish to operate on, or to run automated tests on
        "host" => "pg9_3", # host to use to access the server
        "port" => "5432", # Database port, use default for PostgreSQL
        "database" => "gigadb", # name of the main database, when local used for manual testing and acceptance tests 
        "test_database" => "gigadb_test", # name of the test database, used only for unit tests and functional tests
        "username" => "gigadb", # database username 
        "password" => "", # database password
    ],
    'ftp' => [ # connection details to the ftp server where to download production backup from
        "host" => "", # host for the ftp server
        "username" => "", # ftp username to use to login to ftp server
        "password" => "", # ftp password to use to login to ftp server
    ],
];


```

>**Note:**
> 
> You must specify the ``ftp`` section before moving to the next section


## Populate the local database with a copy of production database backup

```
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --latest
```

This will download and restore the latest production database backup which is from the day before.
If you need a backup for a specific date you can specify a date within the last seven days to the ``--date`` option instead.
Subsequently, you can also pass the ``--nodownload`` to bypass the downloading if you have specified a date you've previously used already.
This is especially useful in automated tests because we don't them slowed down by unnecessary network connections.
The downloaded database backup files are located in the tool's ``sql/`` directory and have file name like ``gigadbv3_########.backup``
where ``########`` represent a date string in the format ``yyyymmdd``.

>**Note:**
>
> Functional and acceptance tests assume the default binary dump for test data is download instead. This is done by replacing ``--latest`` with ``--default``. 
> If a day arrives for which you don't have a copy of the latest production backup yet, the tests will
> fail until you fetch the latest backup again.
 

## Explore the content of the GigadB database using psql

```
$  docker-compose run --rm updater psql -h pg9_3 -U gigadb -d gigadb
```

## Configure the main GigaDB app to talk to the legacy database

This is needed for running the acceptance tests.
It needs to be performed from the root of the gigadb-website project

### 1. After copying over an .env file, make sure Gigadb webapp is running
   
```
$ docker-compose run --rm config
$ docker-compose run --rm webapp
$ ops/scripts/setup_devdb.sh
```

### 2. For acceptance tests, update GigaDB Database configuration

The file is ``protected/config/db.json``

If you've followed the instructions without alterations, replace the values following the table below:

| Key | Value |
| --- | --- |
| database | gigadb |
| host| host.docker.internal|
| user | gigadb |
| password | |



That will connect GigaDB web application to the ``pg9_3`` container service started earlier.
It works because, to Docker containers, ``host.docker.internal`` represents the host where the docker daemon is running
and the previously started ``pg9_3`` container service was exposing the default PostgreSQL port to the host in its ``docker-compose.yml``
configuration.


>In any case, if you have customised the database connection details in the tools' ``config/params.php``
make sure they match here too (we are not talking about operational context here, just running the tests, the tests will refuse to execute if configured host is not the tool's default).

### 3. Make sure GigaDB is reachable 

at ``http://gigadb.gigasciencejournal.com:9170/`` and it shows the production data from the ``pg9_3`` 
database server.

Also make sure the ``chrome`` container service is running as it should (see further down for the reason).

## Run the tests for the tool

Return to the tool's directory
```
$ cd gigadb/app/tools/files-url-updater/ 
```

First, ensure you have downloaded and loaded the default backup for the production database specifically made
for testing the tool:
It contains the same volume as production and has the urls for dataset files that need replacing.
However, any personally identifiable information (PII) have been redacted, and the backup file 
is stored off-site rather than on the ftp server for production backup.

```
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --default
$ ls -alrt sql/gigadbv3_default.backup
-rw-r--r--  1 user  staff  29138712 Aug  4 16:33 sql/gigadbv3_default.backup
```

Configure Codeception:

```
$ docker-compose run --rm updater ./vendor/bin/codecept build
```
This is only needed once or whenever the Codeception configuration has been updated.

Then run all tests suite

```
$ docker-compose run --rm updater ./vendor/bin/codecept run

```

Or run each suite separately:
```
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/unit
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/functional
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/acceptance

```

>Note that due to the need for dropping/restoring the full production database backup before each tests,
> the acceptance tests take  a long time to run (about 9 minutes on my machine)


>**Warning:**
> 
>Don't run the automated tests on remote database setup.
The acceptance tests have a safety mechanism that cause them to exit if they detect 
a database server that's not the database container service "pg9_3".
That's because the acceptance tests use the main database that's configured.
It's not a problem for unit tests and functional tests as long as the test 
database is kept different from the main database.
Nevertheless, it's better to not run any of the suites in an operation context, they are not smoke tests.


>**Note:**
> 
> if you look at the ``tests/acceptance.suite.yml`` configuration file for acceptance test, you will notice the peculiar
> values used to configure ``WebDriver``. That's because the acceptance tests needs to talk to the main locally 
> deployed GigaDB web application in order to perform actions. Since a ``chrome`` container service is already running for 
> its acceptance tests and is exposing the default WebDriver hub port (4444) to the host,
> we don't need to start another instance for this tool's suite and can reuse the already running one.

## About Yii2 runners

You may have noticed that all Yii2 console commands are run by adding the command name as a parameter to the ``yii`` command.
That script is the main runner and is in charge of summoning all the Yii runtime infrastructure to be at the service of the command.
Most notably, that include all the component configuration specified in ``config/console.php``.

There are actually two runners in this project, ``yii`` mentioned above, and also ``yii_test``.
The latter is only used to run commands from within a functional test. eg: 
```
 $I->runShellCommand("echo yes | ./yii_test dataset-files/update-ftp-urls --next 5");
```
Unlike the main runner, ``yii_test`` is reading its configuration from ``config/test.php``.
Because mistakenly using the main runner in the functional tests would have destructive consequences, 
The ``setUp()`` hook of the Functional test class verifies that the functional tests are not making use of it.


## Running the tool to replace ftp urls in ftp_site and location

### 0. Update configuration for operational context

Now we are in an operational context.
This is where we want first to update the ``config/params.php`` file with the connection details
for the target remote database.

>**Note:**
>
>If the target is the current production database, the tool needs to be run within BGI network.

To verify that the tool can communicate with the target dataase server, you can use ``pg_isready``.
For example to do this with the pg9_3 database container:

```
$ docker-compose run --rm updater pg_isready -h pg9_3 -U gigadb -d gigadb
Creating files-url-updater_updater_run ... done
pg9_3:5432 - accepting connections
```


### 1. Run the command with no option to get the usage

```
$ docker-compose run --rm updater ./yii dataset-files/update-ftp-urls
Creating files-url-updater_updater_run ... done

Usage:
        ./yii dataset-files/update-ftp-url
        ./yii dataset-files/update-ftp-url --config
        ./yii dataset-files/update-ftp-url --next <batch size> [--after <dataset id>][--dryrun][--verbose]

```

>**Note:**
> 
> Making a call to the command with only the ``--config`` option will show the current key/value pairs held 
in the ``config/params.php`` configuration file used to configure target database and ftp server for getting the DB backup files.
> Use it to check the changes you've just made.

### 2. Always run in dry-run mode before running it for real

```
$  docker-compose run --rm updater ./yii dataset-files/update-ftp-urls --next 2 --dryrun --verbose
```

The ``--verbose`` option is important and allow you to see exactly what url of which table row 
is going to be replaced and by which new url.

### 3. Batch operation

Everytime the command is run, it will run the next batch (of specified size) of pending datasets (datasets whose urls need replacement)

```
$ docker-compose run --rm updater ./yii dataset-files/update-ftp-urls --next 2 --verbose
Creating files-url-updater_updater_run ... done

Warning! This command will alter 2 datasets in the database, are you sure you want to proceed?
 (yes|no) [no]:yes
Executing command...
[6]     Transforming ftp_site... DONE
        Transforming file locations ...DONE (5/5)
[7]     Transforming ftp_site... DONE
        Transforming file locations ...DONE (188/188)

...
$ docker-compose run --rm updater ./yii dataset-files/update-ftp-urls --next 2 --verbose
Creating files-url-updater_updater_run ... done

Warning! This command will alter 2 datasets in the database, are you sure you want to proceed?
 (yes|no) [no]:yes
Executing command...
[8]     Transforming ftp_site... DONE
        Transforming file locations ...DONE (7/7)
[9]     Transforming ftp_site... DONE
        Transforming file locations ...DONE (469/469)
...
```

### 4. Completion

When there's no more pending datasets left, the command will show a message

```
$ docker-compose run --rm updater ./yii dataset-files/update-ftp-urls --next 2 --verbose
Creating files-url-updater_updater_run ... done

There are no pending datasets with url to replace.

```

## Provisioning

The tool run as a Docker container. Configuration is performed in two well commented files:
 
 * ``Dockerfile``
 * ``docker-compose.yml``

The database container service described in the latter file uses the official Postgres Docker image which has its documentaiton here:

 * https://hub.docker.com/_/postgres

## Troublehshooting

If the functional tests and acceptance tests suites have failing tests
due to database errors about non existent tables:

<hr>
1. check that you have downloaded the latest database backup for the day as described earlier in this doc
<hr>
2. verify that the backup downloaded from the ftp server is not corrupted:


>This can be performed the following way:
First download the backup for the last few days:

```
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210710 --norestore
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210711 --norestore
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210712 --norestore
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210713 --norestore
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210714 --norestore
```

>And list them:

```
$ ls -al sql
total 230120
drwxr-xr-x@ 11 rijamenage  staff       352 Jul 15 10:12 .
drwxr-xr-x@ 23 rijamenage  staff       736 Jul 15 10:00 ..
-rw-r--r--   1 rijamenage  staff       164 Jul 14 09:30 bootstrap_gigadb.sql
-rw-r--r--   1 rijamenage  staff     88048 Jul 14 09:30 gigadb_tables.sql
-rw-r--r--   1 rijamenage  staff  29156662 Jul 10 11:42 gigadbv3_20210710.backup
-rw-r--r--   1 rijamenage  staff  29154471 Jul 11 11:47 gigadbv3_20210711.backup
-rw-r--r--   1 rijamenage  staff  29156654 Jul 12 11:48 gigadbv3_20210712.backup
-rw-r--r--   1 rijamenage  staff  29156933 Jul 13 11:48 gigadbv3_20210713.backup
-rw-r--r--   1 rijamenage  staff    115304 Jul 14 11:52 gigadbv3_20210714.backup
-rw-r--r--   1 rijamenage  staff     23199 Jul 14 09:30 pg_restore.list
-rwxr-xr-x   1 rijamenage  staff       537 Jul 14 09:30 repopulate_testdb.sh
```

>Check whether all backups have file size that seem reasonable.
Here you can see the backup for 20210714 has abnormal file size, it must be corrupted.

<hr>

3. Notify DB backup team of the problem
<hr>
4. Apply workaround which consists of copying a previous working backup into the filename of the corrupted backup:

```
$  cp sql/gigadbv3_20210712.backup sql/gigadbv3_20210714.backup
```
<hr>
 5. functional tests and acceptance tests should be all passing
<hr>