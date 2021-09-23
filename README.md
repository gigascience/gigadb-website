# GigaDB

[![pipeline status](https://gitlab.com/gigascience/upstream/gigadb-website/badges/develop/pipeline.svg)](https://gitlab.com/gigascience/forks/gigadb-website/commits/develop)
[![coverage report](https://gitlab.com/gigascience/upstream/gigadb-website/badges/develop/coverage.svg)](https://gitlab.com/gigascience/forks/gigadb-website/commits/develop)

## What is it?

[GigaDB](http://gigadb.org) is a database which provides information
about datasets associated with scientific papers published in
[GigaScience](http://gigascience.biomedcentral.com). Links to these
datasets are also available from GigaDB. The datasets are
freely-accessible and can be obtained from a file server hosted by
[BGI](http://www.genomics.cn/en/index).

This repository contains the source code for running the [GigaDB](http://gigadb.org)
website. This current release is version 3.1.

## Installation

### Requirements

* Docker (version 18 or more recent) is [installed](https://www.docker.com/products/docker-desktop) 
on your machine (Windows or macOS)
* You have a [GitLab account](https://gitlab.com/), which is a member of the 
[Gigascience Forks group](https://gitlab.com/gigascience/forks), so you can 
access the application's [secret variables](https://docs.gitlab.com/ee/api/README.html)
* You have generated a [personal access token](https://docs.gitlab.com/ee/user/profile/personal_access_tokens.html) 
from your GitLab user settings so your local setup can access the secret 
variables
* You have git cloned the [gigascience/gigadb-website](https://github.com/gigascience/gigadb-website)
project locally under `gigadb-website`

### Get started quickly

```
$ cd gigadb-website
$ ./up.sh
```
This will start up all necessary services, perform the database migrations, generate the configuration and reference data feeds.
It will also select the "dev" set of test data for the local development environment.

To select a different set of data for the local development database, you can specify the set you want to use:

```
$ ./up.sh dev
$ ./up.sh gigadb_testdata
$ ./up.sh production_like
```

>**Note 1**: You can run the script anytime you want to reset the entire state of the codebase, not just the first time.
 
>**Note 2**: You can also read, pick and choose the steps in ``up.sh`` for a more manual and adhoc setup or just to understand how it works

#### About the ``--build`` argument

The ``--build`` argument (to force-build containers) is required :

* after changing their Dockerfile 
* after making changes to the ``.env`` file
* after switching/merging git branches with either of the above conditions

to ensure the containers (**application**, **web**, and **test**) are built from the expected local development files due to lingering prior running version of the containers. 

Avoiding using it when not necessary will speed up container startup by not building them again. Even if not used, the containers will still automatically go through the build process the very first time or after the container image has been deleted.

### Running database migrations

Some code changes are database schemas changes. You will need to run Yii migration to create postgresql database used by GigaDB as follows:
```
# Create schema tables
$ docker-compose run --rm  application ./protected/yiic migrate --migrationPath=application.migrations.schema --interactive=0
# Create migration scripts for uploading data
$ docker-compose up csv-to-migrations
# Upload data into tables
$ docker-compose run --rm  application ./protected/yiic migrate --migrationPath=application.migrations.data.dev --interactive=0
```

>Note 1: When creating database migrations for changes to the database schema, ensure any creation of entity only happens if it doesn't exist already, i.e use: 
> * ``CREATE TABLE IF NOT EXISTS``
> * ``CREATE SEQUENCE IF NOT EXISTS``
> * ``CREATE OR REPLACE VIEW``

>Note 2: Occasionally, you may need to import database dumps and run the database migrations afterwards.
> This will fail unless you run the following commands before the migrations in order to first drop existing constraints and indexes:
> ```
> $ docker-compose run --rm application ./protected/yiic custommigrations dropconstraints 
> $ docker-compose run --rm application ./protected/yiic custommigrations dropindexes 
>```


### Configuration variables

The project can be configured using *deployment variables* managed in `.env`, 
*application variables* managed in the [docker-compose.yml](ops/deployment/docker-compose.yml) 
file and its overrides (`docker-compose.*.yml`). There is a second type of variables called secrets for passwords, api keys 
and tokens and they are stored as *secret variables* in `.secrets` for the application access.

When using the ``up.sh`` script to setup the project, both `.env` and `.secrets` will be automatically generated.
The generation of the former will require two inputs from the user, the GitLab private tokens and the name of the user's fork of GigaDB in
GitLab's ``gigascience/Forks`` group.

the content .env is meant to be manually tweaked by the developer to suit is particular development environment or the nature of the work is engaged with.
The secret variables' values are sourced from a password vault in GitLab. The variables in the password vault are organised in a hierarchy of groups:
```
GitLab
└── gigascience
    ├── Forks
    │   ├── alice-gigadb-website
    │   └── bob-gigadb-website
    └── Upstream
        └── gigadb-website
```

A variable can be defined at any level, but the closer to the leaf, the more prevailing an assignment is.
The higher level are for variables that have the same value for most setups (e.g: the develpoment url is likely to be the same for Alice, Bob and other developers).
If there is a need to customise the value of a variable, we just need to define it at a lower level and that assigment will take precedence (e.g: Bob needs to use a different port for development url as the default port is used already on his computer).
Variables that are specific to each developer should be defined only at the leaf level (a developer's fork)

## Testing GigaDB 

Make sure the test container is built if it's the first time or if the Dockerfile and/or the ``.env`` file have changed to ensure the container uses the correct development files.

```
$ docker-compose build test
```

### Using convenience test runners:

```
$ ./tests/unit_runner         # run all the unit tests after ensuring test DB migrations are up-to-date
$ ./tests/functional_runner   # run all the functional tests after ensuring DB migrations are up-to-date
$ docker-compose up -d chrome  # start a headless web browser
$ ./tests/acceptance_runner local # run all the acceptance tests (see important note below)
$ ./tests/coverage_runner     # run test coverage, print report and submit to coveralls.io
```

>**Note**: those runners are just ``bash`` wrappers to ``docker-compose run`` commands that you are free to use directly. You will want to do that if you want to tweak the test suite execution (see below).

### How to run a subset of a test suite?

#### For unit tests

You can specify the specific test file you want to run:

```
$ docker-compose run --rm test ./bin/phpunit --testsuite unit --bootstrap protected/tests/unit_bootstrap.php --verbose --configuration protected/tests/phpunit.xml --no-coverage protected/tests/unit/DatasetDAOTest.php
```

#### For functional tests

You can specify the specific test file you want to run:

```
$ docker-compose run --rm test ./bin/phpunit --testsuite functional --bootstrap protected/tests/functional_custom_bootstrap.php --verbose --configuration protected/tests/phpunit.xml --no-coverage protected/tests/functional/SiteTest.php
```

#### For Acceptance tests

three methods are available and they can all be combined together

##### using filename

You specify a feature file to run:

To run the tests:
```
$ docker-compose run --rm test bin/behat --profile local --stop-on-failure features/dataset-admin.feature
```

##### using tags

You can pass tags as arguments to run all scenarios that have these tags in any feature file.

```
$ docker-compose run --rm test bin/behat --profile local --stop-on-failure --tags @login
```

##### using line numbers

You can specify the line number in a feature file where the text of a scenario starts to just run that scenario.

```
$ docker-compose run --rm test bin/behat --profile local --stop-on-failure features/dataset-admin.feature:76
```

## Troubleshooting

### using Portainer

When working on your local dev environment, navigate to ``http://localhost:9009``.
Portainer will give you quick access to all the details about all running container
as well as controls to **start/stop/kill/restart/pause/resume** them.
More importantly, you can easily acces the logs or drop into console mode by
clicking the relevant icon next to the container.f

The only pre-requisite is that a PORTAINER_BCRYPT variable is defined in the ``.env`` file.
Look at the ``env-sample`` file for inline instructions on how to generate a correct value for 
your chosen password.

On production environment, the variable will be exposed from GitLab Group variable.

### using CLI

To access the services logs, use the command below:

```
$ docker-compose logs <service name>            # e.g: docker-compose logs web
```

You can get information on the images, services and the processes running in 
them with these commands:

```
$ docker-compose images
$ docker-compose ps
$ docker-compose top
```

To debug the configuration or the tests, you can drop into `bash` with both 
containers:

```
$ docker-compose run --rm config bash
```

or:

```
$ docker-compose run --rm test bash
```

Both containers have access to the application source files, the Yii framework 
and Nginx site configuration (so they can be used to debug the running web 
application too).

The **test** container has also the PostgreSQL admin tools installed (pg\_dump, 
pg\_restore, psql), so it's a good place for debugging database issues. For
example, to access the PostgreSQL database from the **test** container:

```
# Drop into bash in test container
$ docker-compose run --rm test bash
# Use psql in test container to connect to gigadb database in database container
root@16b04afd18d5:/var/www# psql -h database -p 5432 -U gigadb gigadb
``` 

The test database in the locally-deployed GigaDB application can be populated 
with production-like data as follows:

```
# Drop into bash in the test container
$ docker-compose run --rm test bash
# Access the postgres database using `vagrant` as the password
bash-4.4# psql -h database -p 5432 -U gigadb postgres
Password for user gigadb: 
psql (9.4.21)
Type "help" for help.

postgres=# select pg_terminate_backend(pg_stat_activity.pid) from pg_stat_activity where datname='gigadb';
 pg_terminate_backend 
----------------------
 t
 t
 t
 t
 t
(5 rows)

postgres=# drop database gigadb;
DROP DATABASE
postgres=# create database gigadb owner gigadb;
CREATE DATABASE
postgres-# \q
# Restore the `production_like.pgdmp` database
root@9aece9101f03:/var/www# pg_restore -h database -p 5432 -U gigadb -d gigadb -v ./sql/production_like.pgdmp 
```

>**Note:**
>~~Only~~ The **test** and **application** containers have access to the 
**database** container. In addition, you can access the PostgreSQL RDBMS in the 
database container via the local dockerhost on port 54321. For example, you can
use [pgAdmin](https://www.pgadmin.org) to connect to the gigadb PostgreSQL 
database:

**1.** Click on `Add New Server` and provide a `Name` for the connection in the 
`General` tab.

**2.** Click on the `Connection` tab and enter `localhost` as the `Host name/address` 
and `54321` as the `Port` value. The `Maintenance database` is `gigadb`,  
`username` is `gigadb`, and `password` is `vagrant`.

For further investigation, check out the [docker-compose.yml](ops/deployment/docker-compose.yml) 
to see how the services are assembled and what scripts they run.

## Life cycle

To regenerate the web application configuration files, *e.g.* because a variable 
is added or changed on GitLab or `.env`:

```
$ docker-compose run --rm config
```

To restart, start or stop any of the services:

```
$ docker-compose restart|start|stop <service name>	# e.g: docker-compose restart database
```

To rebuild the local containers (**application** and **test**), e.g: because of 
changes made to the [Dockerfile](ops/packaging/Dockerfile) or because the base 
image has been upgraded (see below):

```
$ docker-compose build <service name>				# e.g: docker-compose build application
```

To tear down all the services (the project data at location pointed at by 
DATA\_SAVE\_PATH *deployment variable* are unaffected):

```
$ docker-compose down
```

To upgrade the images used by the services (including base images for local 
containers) to the latest version of a fixed tag, without restarting the 
services:

```
$ docker-compose pull
```

>**Note**:
>To upgrade the core software to major revision, first change the version 
*deployment variables* in `.env`.

## Generating the documentation

Install mkdocs. On mac you can use brew:

```
$ brew install mkdocs
```

Otherwise you can use Python pip:

```
pip install mkdocs
```

To start the server, from this project root directory, run the command:

```
$ mkdocs serve
```

the documentation will be available at: (http://127.0.0.1:8000)


### PHPDocs


To update the browsable API Docs (PHPDoc), run the command below and then commit 
the changes:
```
$ docker-compose run --rm test ./docs/make_phpdoc
```

## Licensing

Please see the file called [LICENSE](./LICENSE).

