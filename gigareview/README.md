# Giga Review Guide

## Getting started

Go to the gigareview diredctory then start the gigareview application
```
$ cd gigareview
$ ./up.sh

```
>**Note**: In the rest of this doc, we assume we are in the ``gigareview`` directory

## Run the tests

```
$ ./tests/unit_runner
$ ./tests/functional_runner
$ ./tests/acceptance_runner

```
## Review or change configuration of the application

The source of configuration file (examples and templates) are in ``config-sources``
The Yii2 advanced template expects to find the source for each environment in the ``environments`` directory.

``generate_config.sh`` and ``init`` are the scripts to look into to understand how the application is configured. 
The former will interpolate variables from Gitlab with placeholders in template file and generate the configuration in the ``environments`` directory.
The latter will copy the files in the selected environment subdirectory from ``environments`` directory into the configuration directory of each sub-applications 

## Local deployment

The Gigareview system is instantiated using Docker compose services, as defined in ``docker-compose.yml``
and orchestrated in ``up.sh``


# More detailed information (not necessary to get started)

## How was the project bootstrapped (for info only)

1. Create project structure
```
$ docker-compose run --rm test composer create-project --prefer-dist yiisoft/yii2-app-advanced gigareview
```

2. Update ``docker-compose.yml``

3. Update ``Dockerfile`` for each module

4. create ``env-sample``

5. Ensure canonical location for configuration files remains the ``environments`` directory

6. Ensure shared configuration is created in the ``common`` sub-directory of the above directory

7. Update ``.gitignore`` to reflect the configuration strategy

## how were the tables and ActiveRecord classes created (for info only)

Example of Ingest business object for managing EM ingest workflow:

Creating the database migration for the table:
```
$ docker-compose run --rm console ./yii migrate/create create_ingest_table --fields="file_name:string,report_type:integer,fetch_status:smallinteger,parse_status:smallinteger,store_status:smallinteger,remote_file_status:smallinteger,created_at:biginteger, updated_at:biginteger"
```

Creating the corresponding model class
```
$ docker-compose run --rm console ./yii gii/model --ns="common\models" --tableName="ingest" --modelClass="Ingest"  
```
Create the unit test for that model class

```
$ docker-compose run --rm console ./vendor/codeception/codeception/codecept -c /app/common/codeception.yml generate:test unit Ingest
```

Create Feature file for BDD (Gherkin) style acceptance tests:

```
$ docker-compose run --rm console ./vendor/codeception/codeception/codecept -c console/codeception.yml generate:feature acceptance FetchReports
```