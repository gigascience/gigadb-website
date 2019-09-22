# Installing GigaDB using Docker

A test instance of the GigaDB website can be automatically installed on your 
computer as a multi-container application. This involves using 
[Docker](https://www.docker.com), a computer program which allows separate 
applications together with their software dependencies and configuration files 
to be run from within multiple containers. These containers are all isolated 
from one another yet can communicate with each other through well-defined 
channels. Containers are run by a single operating-system kernel and are 
therefore more lightweight than virtual machines which were previously used by 
GigaDB as a development environment.

Containers are created from images which specify their contents. In GigaDB, 
several containers are used for implementing the website application and these 
are specified within the docker-compose.yml file. Most of these are standard 
images downloaded from the public repositories, for example the PostgreSQL 
database system, but others such as web are modified before their use. 

## Preparation

The GigaDB code base is available from 
[GitHub](https://github.com/gigascience/gigadb-website) which can be downloaded
using git. [Docker](https://www.docker.com) is then used to deploy a local 
GigaDB web application.

### Linux

If you want to install the basic Git tools on Linux via a binary
installer, you can generally do so through the basic package
management tool that comes with your distribution. If you’re on
Fedora and Centos for example, open a commandline terminal and use yum:
```bash
$ sudo yum install git-all
```

If you’re on a Debian-based distribution like Ubuntu, try apt-get:
```bash
$ sudo apt-get install git-all
```

Running a local GigaDB web application also requires installing a current 
version of Docker CE and Docker Compose. For Linux Mint 19, this can be done as 
follows: 
```
# Install docker dependencies
$ sudo apt-get update
$ sudo apt-get -y install apt-transport-https ca-certificates curl software-properties-common
# Add Docker GPG key to sign for Docker packages
$ curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
# Add Docker upstream repository to install latest stable release of Docker
$ sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(. /etc/os-release; echo "$UBUNTU_CODENAME") stable"
# Install Docker Engine
$ sudo apt-get update
$ sudo apt-get -y install docker-ce
# Add your normal user to the group to run docker commands as non-privileged user
$ sudo usermod -aG docker $USER
# Log out and log back in so that your group membership is re-evaluated
```

In Linux Mint 19, its package manager will install an old version of the Docker 
Compose tool. A more recent binary can be downloaded from 
[GitHub](https://github.com/docker/compose/releases/):
```
# Install jq command-line JSON processor
$ sudo apt-get install jq
$ VERSION=$(curl --silent https://api.github.com/repos/docker/compose/releases/latest | jq .name -r)
$ DESTINATION=/usr/local/bin/docker-compose
$ sudo curl -L https://github.com/docker/compose/releases/download/${VERSION}/docker-compose-$(uname -s)-$(uname -m) -o $DESTINATION
$ sudo chmod 755 $DESTINATION
# If you get a docker-compose not found in /usr/bin/docker-compose
$ sudo ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose
```

### MacOSX

There are several ways to install Git on a Mac. The easiest is
probably to install the Xcode Command Line Tools. On Mavericks (10.9)
or above, you can do this by trying to run git from the Terminal the
very first time. If you don’t have it installed already, it will
prompt you to install it.

If you want a more up to date version, you can also install git via a
binary installer. An OSX Git installer is maintained and available
for download at the [Git website](http://git-scm.com/download/mac).

Docker for MacOSX can then be installed using these 
[instructions](https://docs.docker.com/docker-for-mac/install/).

### Windows

We suggest that you install [Babun](http://babun.github.io) which
provides a Linux-like console on Windows platforms. Babun will provide
`git` as well as other develop tools.

Docker for Windows can then be installed using these 
[instructions](https://docs.docker.com/docker-for-windows/install/).


## Other requirements

* You have a [GitLab account](https://gitlab.com/), which is a member of the 
  [Gigascience Forks group](https://gitlab.com/gigascience/forks), so you can 
  access the application's 
  [secret variables](https://docs.gitlab.com/ee/api/README.html)
* You have generated a [personal access token](https://docs.gitlab.com/ee/user/profile/personal_access_tokens.html) 
  from your GitLab user settings so your local setup can access the secret 
  variables

## Downloading the GigaDB code repository

After you have git installed, you can now use it to download the GigaDB source 
code from Github:
```bash
$ git clone https://github.com/gigascience/gigadb-website.git
Cloning into 'gigadb-website'...
remote: Counting objects: 1657, done.
remote: Compressing objects: 100% (68/68), done.
remote: Total 1657 (delta 25), reused 0 (delta 0), pack-reused 1581
Receiving objects: 100% (1657/1657), 2.33 MiB | 785.00 KiB/s, done.
Resolving deltas: 100% (516/516), done.
Checking connectivity... done.
```

## Getting started in 3 steps

**(1)** To setup the web application locally, do the following:
```
$ cd gigadb-website                                 # Your cloned git repository for GigaDB website
$ git checkout develop                              # Currently the only branch for which this work
$ cp ops/configuration/variables/env-sample .env    # Make sure GITLAB_PRIVATE_TOKEN is set to your personal access token and GIGADB_ENV=dev
# Check .env file to see if the correct GROUP_VARIABLES_URL and PROJECT_VARIABLES_URL are used!!!
$ docker-compose run --rm config                    # Generate the configuration using variables in .env, GitLab, then exit
```

>**Note 1**:
> A `.secrets` file will be created automatically and populated using secrets 
variables stored in GitLab.

>**Note 2**:
> If you are not a member of the Gigascience Forks GitLab group, you will have 
to provide your own values for the necessary variables using 
`ops/configuration/variables/secrets-sample` as starting point:

>```
>$ cp ops/configuration/variables/secrets-sample .secrets
>$ vi .secrets
>```

**(2)** To start the web application, run the following command:
```
$ docker-compose run --rm webapp                    # Run composer update, then spin up the web application's services, then exit
```

The **webapp** container will run composer update using the `composer.json` 
generated in the previous step, and will launch three containers named **web**, 
**application** and **database**, then it will exit. It's ok to run the command 
repeatedly.

**(3)** Upon success, three services will be started in detached mode.

You can then navigate to the website at:
* [http://gigadb.gigasciencejournal.com:9170](http://gigadb.gigasciencejournal.com:9170/)

>**Note**:
>The first time, it will take longer to start the services as the 
**application** container needs to be built first.

To list the containers making up the GigaDB website:
```
$ docker ps -a
CONTAINER ID        IMAGE                      COMMAND                  CREATED             STATUS              PORTS                                                      NAMES
ca7293d9b0e1        edyan/xhgui                "/root/entrypoint.sh"    4 days ago          Up 11 minutes       9000/tcp, 0.0.0.0:27017->27017/tcp, 0.0.0.0:8888->80/tcp   deployment_xhgui_1
e7beb1fa6c3a        deployment_web             "nginx -g 'daemon of…"   4 days ago          Up 11 minutes       0.0.0.0:9170->80/tcp, 0.0.0.0:8043->443/tcp                deployment_web_1
26dacb2f84c5        postgres:9.4-alpine        "docker-entrypoint.s…"   4 days ago          Up 11 minutes       0.0.0.0:54321->5432/tcp                                    deployment_database_1
45c05692c466        deployment_application     "docker-php-entrypoi…"   4 days ago          Up 11 minutes       9000/tcp                                                   deployment_application_1
0773ea9a6643        wernight/phantomjs:2.1.1   "phantomjs --webdriv…"   4 days ago          Up 11 minutes       8910/tcp                                                   deployment_phantomjs_1
```

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

To only run unit tests for all webapps, use the bash script below:
```
$ ./tests/unit_runner
```

you can read that script to see how to run the test for individual webapps.
For example, to just run the unit tests for GigaDB app, use the following command:

```
$ docker-compose run --rm test ./bin/phpunit --testsuite unit --bootstrap protected/tests/unit_bootstrap.php --verbose --configuration protected/tests/phpunit.xml --no-coverage
```

## Troubleshooting

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

To update the browsable API Docs (PHPDoc), run the command below and then commit 
the changes:
```
$ docker-compose run --rm test ./docs/make_phpdoc
```

## Licensing

Please see the file called [LICENSE](./LICENSE).
