# GigaDB File Upload Wizard

Web application for authors to upload dataset of accepted papers, for reviewers to audit the uploaded dataset and for curators to publish the dataset to the public.

## Start the GigaDB and File Upload Wizard webapps

```
$ socat TCP-LISTEN:2375,reuseaddr,fork UNIX-CONNECT:/var/run/docker.sock &
$ docker-compose up -d gigadb fuw
$ docker-compose exec console /app/yii migrate --interactive=0
$ docker-compose up -d web
$ docker-compose up -d phantomjs
```
**Note:** socat, which needs to be installed (using brew on macOS) is only necessary when running Docker Desktop for macOS and Windows. Not required on Linux.

## Start the prototype

Configure URLs:
```
$ docker-compose exec console bash -c "cd /app;./yii prototype/setup --appUrl http://gigadb.gigasciencejournal.com:9170"
```
To Start the prototype after having started the GigaDB and File Upload Wizard as above:

```
$ docker-compose up -d fuw-proto
$ docker-compose exec web ash -c 'rm /etc/nginx/sites-enabled/gigadb.dev.http.conf'
$ docker-compose exec web /usr/local/bin/enable_sites gigadb-proto.dev.http
```

To stop and disable the prototype after it has been started as above:

```
$ docker-compose exec web ash -c 'rm /etc/nginx/sites-enabled/gigadb-proto.dev.http.conf'
$ docker-compose exec web /usr/local/bin/enable_sites gigadb.dev.http
$ docker-compose stop fuw-proto
```

The protototype will be availabe at:
(http://gigadb.gigasciencejournal.com:9170/proto/)

## Start and accessing the documentation server

Install mkdocs. On mac you can use brew:

```
$ brew install mkdocs
```
start the server:

```
$ cd fuw
$ mkdocs serve
```

the documentation will be available at: (http://127.0.0.1:8000)


## Workflow

*right click and view image to zoom in*

![File Upload Wizard Workflow](img/workflow.png)
