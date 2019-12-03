# GigaDB File Upload Wizard (FUW)

Web application for authors to upload dataset of accepted papers, for reviewers to audit the uploaded dataset and for curators to publish the dataset to the public.

## How to start the GigaDB and File Upload Wizard webapps

A list of service used for File Upload Wizard are in the [Software Architecture](software_architecture.md) document.

### Preparation

For security reasons, inter-container communication is done through Docker Daemon API which use TCP port 2375 (2376 on staging and production).
However on macOS, Docker For Mac doesn't expose the API on a TCP port, so we need to use socat to expose the unix socket on port 2375.

on a separate terminal window/tab, run:
```
$ socat TCP-LISTEN:2375,reuseaddr,fork UNIX-CONNECT:/var/run/docker.sock &
```
socat can be installed with brew: ``brew install socat``

If you prefer seeing the (verbose) logging of container-to-daemon communication (HTTP REST), you can use the ``-v`` argument:

```
$ socat -v TCP-LISTEN:2375,reuseaddr,fork UNIX-CONNECT:/var/run/docker.sock
```

Alternatively, you can avoid local install of socat and run this docker command instead:
```
$ docker run -d -v /var/run/docker.sock:/var/run/docker.sock -p 127.0.0.1:2375:2375 bobrik/socat TCP-LISTEN:2375,fork UNIX-CONNECT:/var/run/docker.sock
```

**Note:** 
> On Windows and Linux, it is not necessary to go through this step as the Docker daemon on those platforms can be configured to operate on a TCP port.

### Run GigaDB, File Upload Wizard API, without prototype from scratch:

```
$ docker-compose run --rm config
$ docker-compose run --rm gigadb
$ docker-compose run --rm less
$ docker-compose run --rm fuw-config
$ docker-compose run --rm fuw
$ docker-compose up -d web
$ docker-compose exec web /usr/local/bin/enable_sites fuw-backend.dev.http
```

**Note:** 
>this assume that there is no Postgres data directory in $DATA\_SAVE\_PATH, so all the necessary database initialization will be run.
If there is one already, you can either delete it or use the command from the next section to run the DB initializations required for FUW.


**Note 2:** 
>On a mac, make sure the output of ``docker-compose run --rm fuw-config`` is as below:

>```
$ docker-compose run --rm fuw-config
Current working directory: /var/www
An .env file is present, sourcing it
Sourcing secrets
Writing REMOTE_DOCKER_HOSTNAME to params-local as 'tcp://host.docker.internal:2375'
```

>On a mac, if doesn't say ``host.docker.internal``, re-run the command, sometimes Docker is slow to make the special hostname available. If it is not written to params-local config, there will be "**Connection Time Out**" or "**Connection Refused**" errors when running the backend functional tests.

### If GigaDB is already deployed:

```
$ docker-compose exec database bash -c 'psql -U gigadb < /docker-entrypoint-initdb.d/3-fuw.sql'
$ docker-compose run --rm fuw-config
$ docker-compose run --rm fuw
$ docker-compose exec web /usr/local/bin/enable_sites fuw-backend.dev.http
```

**Note:** 
>The ``docker-compose exec database`` command is to create DB schema and DB user to support FUW's workflow. That sql bootstrap file is normally run when a new Postgresql is launched with no existing data directory in $DATA\_SAVE\_PATH.
>However, If you have launched GigaDB before, there is already a data directory, so the initialization scripts won't run again, so we need to bootstrap FUW's database manually. There's no problem in running more than once, it will just print errors if the resources already exist.


### Force building containers

If our application containers don't exist yet, Docker will build them automatically before instantiating them.
However, there are situations where we need to force the build process to make sure we have the right containers running: 

* after changing the Dockerfile of the containers
* after making changes to the ``.env`` file
* after switching/merging git branches that had either of the above changes

In those cases, use the ```--build`` argument when you start the container services:

```
$ docker-compose run --rm --build fuw
```


### Run database migrations for File Upload Wizard

Make sure that both the ``fuw`` service (as Composer vendors libraries are required) and the ``fuw-config`` service (that fill in Database connection strings in config files) have been run beforehand.

Main database:
```
$ docker-compose exec console /app/yii migrate --interactive=0
```

Test database:

```
docker-compose exec -T console /app/yii_test migrate --interactive=0
```

**Note**:
>If you forget to run the two commands above, don't worry: they will be applied whenever you execute the test runners for functional tests and unit tests respectively (see below).


## Testing

To run all the tests (GigaDB and FUW), these commands can be used:
```
$ ./tests/unit_runner
$ ./tests/functional_runner
$ ./tests/coverage_runner
$ docker-compose up -d phantomjs
$ ./tests/acceptance_runner local
```
See the [Developer Guide](developer_guide.md) for detailed information.

### Start the prototype


First we need to create config file for the prototype:
```
$ docker-compose exec console bash -c "cd /app;./yii prototype/setup --appUrl http://gigadb.gigasciencejournal.com:9170"
```
Then, to start the prototype after having started the GigaDB and File Upload Wizard API as above:

```
$ docker-compose up -d fuw-proto
$ docker-compose exec web ash -c 'rm /etc/nginx/sites-enabled/gigadb.dev.http.conf'
$ docker-compose exec web /usr/local/bin/enable_sites gigadb-proto.dev.http
```

To stop and disable the prototype after it has been started:

```
$ docker-compose exec web ash -c 'rm /etc/nginx/sites-enabled/gigadb-proto.dev.http.conf'
$ docker-compose exec web /usr/local/bin/enable_sites gigadb.dev.http
$ docker-compose stop fuw-proto
```

The protototype will be availabe at:
[http://gigadb.gigasciencejournal.com:9170/proto/](http://gigadb.gigasciencejournal.com:9170/proto/)


## Workflow

*right click and view image to zoom in*

![File Upload Wizard Workflow](img/workflow.png)