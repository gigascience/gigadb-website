# GigaDB File Upload Wizard

Web application for authors to upload dataset of accepted papers, for reviewers to audit the uploaded dataset and for curators to publish the dataset to the public.

## Start the web applications

## Start the prototype

```
./yii prototype/setup --protoUrl http://fuw-proto-dev.pommetab.com:9170/ --apiUrl http://fuw-admin-api/filedrop-accounts --tusUrl http://fuw-proto-dev.pommetab.com:9170/files/
```

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