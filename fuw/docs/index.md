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
Start the prototype (TODO: currently out-of-date):

```
$ docker-compose up -d fuw-proto
$ docker-compose restart web
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
