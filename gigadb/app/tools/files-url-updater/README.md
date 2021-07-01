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

Doing the above is enough to get started with testing: Specifying the DB password is not necessary for running the command line tool
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


## Populate the local database with a copy of production database backup

```
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210608 --nodownload
```

If you need a backup for a specific date you can remove the ``--nodownlaod`` option and 
specify a date within the last seven days to the ``--date`` option.
Use the ``--latest`` option instead of ``--date`` to download the latest backup (from the day before).

Furthermore ``--default`` and ``--date 20210608`` are equivalent and refer to the database dump stored in the project for testing purpose. 

Beware, before the tool can download a backup of production database, you need to ensure
the ``host``, ``username``, and ``password`` keys of the ``ftp`` section of ``config/params.php``
are appropriately specified. Use the ``--config`` option on its own to show the current value for the DB and FTP settings.

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

Configure Codeception:

```
$ cp tests/acceptance.suite.yml.example tests/acceptance.suite.yml
$ docker-compose run --rm updater ./vendor/bin/codecept build
```
This is only needed once or after the Codeception configuration has been updated.

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

The configuration for unit and functional tests don't need customisation so far.
But the one for acceptance tests might need adjustments, that's why an example 
is provided as a ``tests/acceptance.suite.yml.example`` file to copy from.
Even so, the default values in the example should work out of the box 
if you've just been following the instructions with no alteration so far.

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
> if you look at the ``acceptance.suite.yml`` configuration file for acceptance test, you will notice the peculiar
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


