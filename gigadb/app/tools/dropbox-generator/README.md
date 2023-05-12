# DROPBOX-GENERATOR TOOL

## Preparation

Generate the config/params.php file so the tool has the credentials to access 
the Wasabi API:
```
$ cd gigadb/app/tools/dropbox-generator
$ ./configure
```
> params.php should be present in the `config` directory.

Install Composer dependencies:
```
$ docker-compose run --rm tool composer install
# Update composer packages
$ docker-compose run --rm tool composer update
```

## Using console commands to perform functions

Functions in Controller classes can be called from the command-line to execute
tasks in Wasabi. For example, to read a file in Wasabi bucket using the
`actionRead()` function the `WasabiBucketController` class, execute this:
```
$ docker-compose run --rm tool /app/yii wasabi-bucket/read --bucket dbgiga-datasets --filePath "live/pub/10.5524/102001_103000/102304/bar.txt"
```

## Create a user dropbox for an author to upload data

A bash script called `createAuthorDropbox.sh` contains the necessary steps to 
create a Wasabi bucket for an author to upload their files to. This script
requires the manuscript identifier in lowercase as a parameter:
```
$ docker-compose run --rm tool /app/createAuthorDropbox.sh --manuscript-id giga-d-23-00288
```

### Functional tests

There is a functional test which checks the `actionCreategigadbuser()` function in
`WasabiController`.
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional

# Run single test
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiUserCest.php

$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiBucketCest.php

$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiPolicyCest.php

# Run functional test to check user dropbox creation workflow
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiDropboxCest.php
```
