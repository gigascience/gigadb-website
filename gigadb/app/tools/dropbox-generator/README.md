# DROPBOX-GENERATOR TOOL

## Preparation

### Configuration for `dev` environment

Create a `.env` file based the `env.example` file in `config` directory.
```
$ cd gigadb/app/tools/dropbox-generator
$ cp config-sources/env.example .env
```
> Ensure you provide values for the GITLAB_PRIVATE_TOKEN and GitLab REPO_NAME
> variables.

Generate the `config/params.php` file so the tool has the credentials to access 
the Wasabi API:
```
$ ./configure
```
> params.php should be present in the `config` directory.

Install Composer dependencies:
```
$ docker-compose run --rm tool composer install
# Update composer packages
$ docker-compose run --rm tool composer update
```

## Create a user dropbox for an author to upload data

A bash script called `createAuthorDropbox.sh` contains the necessary steps to 
create a Wasabi bucket for an author to upload their files to. This script
requires the manuscript identifier in lowercase as a parameter:
```
$ ./createAuthorDropbox.sh --manuscript-id giga-d-23-00288
```

> The manuscript identifier cannot contain upper case letters since they are
> not allowed in Wasabi bucket names

## Functional tests

There is a functional test which checks the workflow for creating a dropbox
bucket in Wasabi:
```
# Run functional test to check user dropbox creation workflow
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiDropboxCest.php
```

There are functional tests for each of the Controller Command classes:
```
# Run single test
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiUserCest.php

$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiBucketCest.php

# Configure /tests/functional_runner or /tests/acceptance_runner to run specific tests
$ docker-compose run --rm tool ./vendor/codeception/codeception/codecept run --debug tests/functional/WasabiBucketCest.php:^tryCreateBucketWithBucketNameContainingUpperCaseLetters$

$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/WasabiPolicyCest.php
```

All of the functional tests can be executed using:
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional
```