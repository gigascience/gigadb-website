<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Basic Project Template</h1>
    <br>
</p>

Yii 2 Basic Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
rapidly creating small projects.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![build](https://github.com/yiisoft/yii2-app-basic/workflows/build/badge.svg)](https://github.com/yiisoft/yii2-app-basic/actions?query=workflow%3Abuild)

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.6.0.


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
http://localhost/basic/web/
~~~

### Install from an Archive File

Extract the archive file downloaded from [yiiframework.com](http://www.yiiframework.com/download/) to
a directory named `basic` that is directly under the Web root.

Set cookie validation key in `config/web.php` file to some random secret string:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

You can then access the application through the following URL:

~~~
http://localhost/basic/web/
~~~


### Install with Docker

Update your vendor packages

    docker-compose run --rm php composer update --prefer-dist
    
Run the installation triggers (creating cookie validation code)

    docker-compose run --rm php composer install    
    
Start the container

    docker-compose up -d
    
You can then access the application through the following URL:

    http://127.0.0.1:8000

**NOTES:** 
- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches


CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.


TESTING
-------

Tests are located in `tests` directory. They are developed with [Codeception PHP Testing Framework](http://codeception.com/).
By default, there are 3 test suites:

- `unit`
- `functional`
- `acceptance`

Tests can be executed by running

```
vendor/bin/codecept run
```

The command above will execute unit and functional tests. Unit tests are testing the system components, while functional
tests are for testing user interaction. Acceptance tests are disabled by default as they require additional setup since
they perform testing in real browser. 


### Running  acceptance tests

To execute acceptance tests do the following:  

1. Rename `tests/acceptance.suite.yml.example` to `tests/acceptance.suite.yml` to enable suite configuration

2. Replace `codeception/base` package in `composer.json` with `codeception/codeception` to install full-featured
   version of Codeception

3. Update dependencies with Composer 

    ```
    composer update  
    ```

4. Download [Selenium Server](http://www.seleniumhq.org/download/) and launch it:

    ```
    java -jar ~/selenium-server-standalone-x.xx.x.jar
    ```

    In case of using Selenium Server 3.0 with Firefox browser since v48 or Google Chrome since v53 you must download [GeckoDriver](https://github.com/mozilla/geckodriver/releases) or [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/downloads) and launch Selenium with it:

    ```
    # for Firefox
    java -jar -Dwebdriver.gecko.driver=~/geckodriver ~/selenium-server-standalone-3.xx.x.jar
    
    # for Google Chrome
    java -jar -Dwebdriver.chrome.driver=~/chromedriver ~/selenium-server-standalone-3.xx.x.jar
    ``` 
    
    As an alternative way you can use already configured Docker container with older versions of Selenium and Firefox:
    
    ```
    docker run --net=host selenium/standalone-firefox:2.53.0
    ```

5. (Optional) Create `yii2basic_test` database and update it by applying migrations if you have them.

   ```
   tests/bin/yii migrate
   ```

   The database configuration can be found at `config/test_db.php`.


6. Start web server:

    ```
    tests/bin/yii serve
    ```

7. Now you can run all available tests

   ```
   # run all available tests
   vendor/bin/codecept run

   # run acceptance tests
   vendor/bin/codecept run acceptance

   # run only unit and functional tests
   vendor/bin/codecept run unit,functional
   ```

### Code coverage support

By default, code coverage is disabled in `codeception.yml` configuration file, you should uncomment needed rows to be able
to collect code coverage. You can run your tests and collect coverage with the following command:

```
#collect coverage for all tests
vendor/bin/codecept run --coverage --coverage-html --coverage-xml

#collect coverage only for unit tests
vendor/bin/codecept run unit --coverage --coverage-html --coverage-xml

#collect coverage for unit and functional tests
vendor/bin/codecept run functional,unit --coverage --coverage-html --coverage-xml
```

You can see code coverage output under the `tests/_output` directory.

### Updating dataset file URLs with Wasabi prefix

#### Preparation

Go to `gigadb/app/tools/files-metadata-console` and create a .env file: 
```
$ docker-compose run --rm configure
```

Update the .env file with values for REPO_NAME and GITLAB_PRIVATE_TOKEN. Re-run
`docker-compose run --rm configure` to create a .secrets file and other 
configuration files in config directory.

#### Dev environment

To begin update of file URLs in batches of 3 datasets, execute in dry run mode:
```
$ docker-compose run --rm files-metadata-console ./yii update/urls --separator=/pub/ --exclude='100020' --next=3
```
> Dataset 100020 has been excluded so is not processed by the tool.

To make changes to the database, use the `--apply` flag:
```
$ docker-compose run --rm files-metadata-console ./yii update/urls --separator=/pub/ --exclude='100020' --next=3 --apply
```

You can also specify a stop DOI so when the current DOI to be processed is greater
than or equal to the stop DOI then File URL transformation is terminated:
```
$ docker-compose run --rm files-metadata-console ./yii update/urls --separator=/pub/ --exclude='100020' --next=3 --stop='200000' --apply
```

##### Test with production data

```
# Connect to dev database
$ PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb postgres
# Delete connections on a given database
postgres=# SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'gigadb';
# Create new empty database
postgres=# drop database gigadb;
postgres=# create database owner gigadb;
postgres=# \q
# Load database backup file
$ docker-compose run --rm test pg_restore -h database -p 5432 -U gigadb -d gigadb -v "/gigadb/app/tools/files-metadata-console/sql/gigadbv3_20230622.backup"
# Run tool
$ docker-compose run --rm files-metadata-console ./yii update/urls --separator=/pub/ --exclude='100396,100446,100584,100747,100957,102311' --stop=200002 --next=3 --apply 
```

##### Dataset 100396

Dataset 100396 contains nearly 200,000 files which all need to have their location column updating with correct Wasabi 
URL. The tool is not able to work with so many files and so need to update manually. Firstly, update ftp_site column in
dataset table for Dataset 100396:
```
# Connect to dev database
$ PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb postgres
# Delete connections on a given database
postgres=# SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'gigadb';
postgres=# \q
# Load database backup file
$ docker-compose run --rm test pg_restore -h database -p 5432 -U gigadb -d gigadb -v "/gigadb/app/tools/files-metadata-console/sql/gigadbv3_20230619.backup"
# Connect to dev database
$ PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb postgres
# Delete connections on a given database
postgres=# SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'gigadb';
postgres=# \c gigadb;
# Update ftp_site column for dataset
gigadb=# Update dataset set ftp_site = REPLACE(ftp_site, 'https://ftp.cngb.org/pub/gigadb/pub/', 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/') where identifier = '100396';
UPDATE 1
# Test updates have been made
gigadb=# select ftp_site from dataset where identifier = '100396';
                                            ftp_site                                            
------------------------------------------------------------------------------------------------
 https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100396/
(1 row)
```

Next, update all location fields for all files associated with dataset 100396:
```
# Update location column for dataset files - will take about a minute to complete
gigadb=# Update file set location = REPLACE(location, 'https://ftp.cngb.org/pub/gigadb/pub/', 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/') where dataset_id = 629;
UPDATE 197214
# Test updates have been made
gigadb=# select * from file where dataset_id = 629 and id = 287662;
 287662 |        629 | 50930810.pdb | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100396/eModelBDB/810/50930810.pdb | pdb       | 370380 | reactant set ID 50930810 | 2018-07-04 |         6 |     254 | FILE_CODE |             |              0 | 
```

#### Staging and live environments

To complete a thorough transformation of all dataset file location URLs, the 
values of specific command-line parameters should be determined.

1. Identify the DOI of the latest dataset whose data has been migrated to Wasabi.
For example on Tue 26 Sep 2023, it was dataset 102456 for http://gigadb.org. The
value for --stop parameter should therefore be 102457.
2. Determine the list of DOIs that should be excluded from File location URL
updated by consulting the [big dataset spreadsheet](https://docs.google.com/spreadsheets/d/13DVSESKMTewlv11Z7cVNPEBMo4lzQa4X_dB0OFc5N_Y/edit#gid=0).

Drop database triggers otherwise tool will hang due to memory issues:
```
# Log into bastion server using SSH
$ ssh -i ~/.ssh/id-rsa-aws-tokyo-peter.pem centos@bastion-ip 
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'drop trigger if exists file_finder_trigger on file RESTRICT'
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'drop trigger if exists sample_finder_trigger on sample RESTRICT'
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'drop trigger if exists dataset_finder_trigger on dataset RESTRICT'
```

Execute tool (in batches of 50 datasets) in dry run mode to check the
functionality is working:
```
$ docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/urls --separator=/pub/ --stop=102457 --next=50 --exclude='100050,100115,100157,100242,100310,100396,100443,100608,100622,100707,100849,102425,102431,102440,102441'
```

Execute tool until all datasets have had their file locations updated with 
Wasabi links:
```
$ docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/urls --separator=/pub/ --stop=102457 --next=50 --exclude='100050,100115,100157,100242,100310,100396,100443,100608,100622,100707,100849,102425,102431,102440,102441' --apply
```

> The dataset file update process should take up to 1 hour and a half to 
> transform production GigaDB data from start to finish.

Re-create database triggers:
```
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'create trigger file_finder_trigger after insert or update or delete or truncate on file for each statement execute procedure refresh_file_finder()'
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'create trigger sample_finder_trigger after insert or update or delete or truncate on sample for each statement execute procedure refresh_sample_finder()'
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'create trigger dataset_finder_trigger after insert or update or delete or truncate on dataset for each statement execute procedure refresh_dataset_finder()'
```

The information on dataset page displayed to users may not reflect the changes 
to dataset FTP site and file location URLs now in the database because of how
the current GigaDB website caching functionality works. For this reason, the
web application should be restarted to reset the cache on the Gitlab pipeline
web console. This can be using the Pipelines page on the Gitlab web console by
manually executing `*_stop_app` and `*_start_app` jobs. You should now be able
to go to any GigaDB website page for a dataset that is not in the excluded list
and see file links that point to Wasabi.

Manually update file location URLs for those datasets containing too many files
which will result in a memory error, e.g. dataset 100396
```
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'Update file set location = REPLACE(location, 'https://ftp.cngb.org/pub/gigadb/pub/', 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/') where dataset_id = 629;'
```

### Running unit and functional tests in files metadata console tool

Execute all unit tests:
```
$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/unit/DatasetFilesURLUpdaterTest.php
```

Execute single unit test:
```
$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/unit/DatasetFilesURLUpdaterTest.php:^testGetPendingDatasets$
```

Execute functional tests:
```
$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/functional/CheckValidURLsCest.php

$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/functional/ReplaceFileUrlSubstringWithPrefixCest.php
```

Execute single functional test:
```
$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/functional/CheckValidURLsCest.php:^tryNoIssueToReport$

$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/functional/ReplaceFileUrlSubstringWithPrefixCest:^tryExcludingDOIsFromFileUrlChanges$
```
