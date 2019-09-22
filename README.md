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


### Getting started in 3 steps

**(1)** To setup the web application locally, do the following:
```
$ cd gigadb-website                         # your cloned git repository for Gigadb website
$ git checkout develop                      # the branch with the latest code base
$ cp ops/configuration/variables/env-sample .env    # make sure GITLAB_PRIVATE_TOKEN is set to your personal access token
$ docker-compose run --rm config            # generate the configuration using variables in .env, GitLab, then exit
```

>**Note 1**: A `.secrets` file will be created automatically and populated using 
secrets variables stored in GitLab.

>**Note 2**: If you are not a member of the Gigascience's Forks GitLab group, 
you will have to provide your own values for the necessary variables using 
`ops/configuration/variables/secrets-sample` as a starting point:
>```
>$ cp ops/configuration/variables/secrets-sample .secrets
>$ vi .secrets
>```

**(2)** To start the web application, run the following commands:
```
$ docker-compose -d gigadb fuw
$ docker-compose -d web
```

The **gigadb** service will run composer update using the `composer.json` 
generated in the previous step, and will launch three containers named **web**, 
**application** and **database**, then it will exit. It's ok to run the command 
repeatedly.

The **fuw** service will do the same for the File Upload Wizard. You can omit it if not working on File Upload Wizard.

The **web** service will start the web server

If working on File Upload Wizard on a Mac, you will need to enable TCP access to the Docker Daemon API, by running the following command on a separate terminal:

```
$ socat TCP-LISTEN:2375,reuseaddr,fork UNIX-CONNECT:/var/run/docker.sock &
```
(socat can be instaled using ``brew install socat``)



**(3)** Upon success, three services will be started in detached mode.

You can then navigate to the website at:

 * [http://gigadb.gigasciencejournal.com:9170/](http://gigadb.gigasciencejournal.com:9170/)

>**Note**: The first time, it will take longer to start the services as the 
**application** container needs to be built first.


### Configuration variables

The project can be configured using *deployment variables* managed in `.env`, 
*application variables* managed in the [docker-compose.yml](ops/deployment/docker-compose.yml) 
file and its overrides (`docker-compose.*.yml`). Finally, passwords, api keys 
and tokens are managed as *secret variables* in `.secrets`.


### Running database migrations

Some code changes are database schemas changes. To ensure you have the latest 
database schema, you will need to run Yii migration as below:
```
$ docker-compose run --rm  application ./protected/yiic migrate --interactive=0
$ docker-compose exec -T console /app/yii migrate --interactive=0
```
## Testing

To run the tests:
```
$ ./tests/all_and_coverage
```

This will run all the tests and generate a test coverage report. An headless 
Selenium web browser (currently PhantomJS) will be automatically spun-off into 
its own container. If an acceptance test fails, it will leave a screenshot under 
the `./tmp` directory.

To only run unit tests, use the command:
```
$ ./tests/unit_runner
```

For functional tests:
```
$ ./tests/functional_runner
```

## Troubleshooting (for GigaDB)

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