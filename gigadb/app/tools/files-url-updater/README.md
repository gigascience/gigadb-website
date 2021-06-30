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
if you want to use the tool on a remote database (e.g: the production database server)

## Populate the local database with a copy of production database backup

```
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210608 --nodownload
```

If you need a backup for a specific date you can remove the ``--nodownlaod`` option and 
specify a date with the last seven days to the ``--date`` parameter.
Use the ``--latest`` option instead of ``--date`` to download the latest backup (the one from the day before)
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
It needs to be performed from the root of the project

### 1. After copying over an .env file, make sure Gigadb webapp is running
   
```
$ docker-compose run --rm config
$ docker-compose run --rm webapp
```

### 2. Update the Database configuration file ``protected/config/db.json``
   to read as below:

```
{
    "database": "gigadb",
    "host"    : "host.docker.internal",
    "user"    : "gigadb",
    "password": "",
}

```

That will connect GigaDB web applicaiton to the ``pg9_3`` container service started earlier.


### 3. Make sure GigaDB is reachable 

at ``http://gigadb.gigasciencejournal.com:9170/`` and it shows the production data from teh ``pg9_3`` 
database server.


## Run the tests for the tool

Return to the tool directory
```
$ cd gigadb/app/tools/files-url-updater/ 
```

Ensure to configure Codeception:

```
$ cp tests/unit.suite.yml.example tests/unit.suite.yml
$ cp tests/acceptance.suite.yml.example tests/acceptance.suite.yml
$ docker-compose run --rm updater ./vendor/bin/codecept build
```

Then run all tests suite

```
$ docker-compose run --rm updater ./vendor/bin/codecept run

```

## Running the tool to replace ftp urls in ftp_site and location

### 1. run the command with no option to get the usage

```
$ docker-compose run --rm updater ./yii dataset-files/update-ftp-urls
Creating files-url-updater_updater_run ... done

Usage:
        ./yii dataset-files/update-ftp-url
        ./yii dataset-files/update-ftp-url --config
        ./yii dataset-files/update-ftp-url --next <batch size> [--after <dataset id>][--dryrun][--verbose]

```

Notice making a call to the command with only the ``--config`` option will show the current key/value pairs held 
in the ``config/params.php`` configuration file used to configure remote database and ftp server.

### 2. Always run in dry run mode before running it for real

```
$  docker-compose run --rm updater ./yii dataset-files/update-ftp-urls --next 2 --dryrun --verbose
```

The ``--verbose`` option is important and allow you to see exactly what url of which table row 
is going to be replaced and by which new url.

### 3. Batch operation

Everytime the command is run, it will run the next batch of pending datasets (datasets whose urls need replacement)

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


