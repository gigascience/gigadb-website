# Software Architecture for File Upload Wizard

## Architecture

![File Upload Wizard Workflow](img/architecture.png)

## Systems Components

### Web Server

The web server is shared by all web apps, Gigadb and File Upload Wizard.
It also acts as a termination for TLS for the web apps, as well as to the TUS server.
The server software is Nginx opensource running on Alpine Linux.

The docker-compose service for the container is called "web"

### Database Server

The web server is shared by all web apps, Gigadb and File Upload Wizard.
PostgresQL 9.6 is used on non-production environment. It also run on Alpine Linux.

### TUS File server

The method for users to upload their files is to use the File Upload Wizard form that use a javascript library (Uppy) that provide upload UX and client to send the files over Internet to a server implementing the TUS transport protocol.

On the same Docker host that run gigadb website containers, we use a container running the tusd, the official reference implementation of a TUS server. It will exposes the port 1080 and the Uppy javascript client must be configured with its address and port.

In production, the tusd will be fronted by a TLS termination proxy (implemented in the nginx web server container) so the file transfers are encrypted.

The docker-compose service for this container is called "tusd".
The image used is the official image: (https://hub.docker.com/r/tusproject/tusd)

More about tusd: [tusd](https://github.com/tus/tusd)

More about Uppy: [uppy](https://uppy.io/docs/)

### FTP Server

For some users, the form-based file upload cannot works because of web browsers incompatibility or corporate firewall policy.
They can use FTP to upload the files. We use a container running pure-ftpd which is lightweight, easily configurable and very-secure.

FTP is also the method for exposing the links allowing reviewers to download files to assess.

Therefore for each File Upload Wizard Filedrop account, there are two ftp account, one for uploading, the other for downloading. the authorization token is different for each FTP account.

The image is built from our Dockerfile in `fuw/docker-pure-ftpd`, based on its most popular docker image: (https://github.com/stilliard/docker-pure-ftpd)

The docker-service for this container is called "ftpd".

More about ftpd: [pure-ftpd](https://www.pureftpd.org/project/pure-ftpd/)

### Hooks, watcher and files management

There is a file pipeline that kicks off server-side as soon as a file has finished uploading.

Depending on the upload method used (tus or ftp), the uploaded file is moved using post-upload hook (tus) or file-system watching (ftp). The aim being move the files in a common area from which they can then be stored, served and eventually synchronised (to public ftp server on CNGB).

The tusd hooks uses its hooks feature: [tusd hooks](https://github.com/tus/tusd/blob/master/docs/hooks.md).

For the ftp uploads, we watches the filesystem using linux inotify command.
We use a container that run linux inotify and a python daemon to execute configured commands upon file change.

The image for this container is custom built and is in ``fuw/docker-inotify-command``. It is based on third party image: (https://github.com/coppit/docker-inotify-command).

The commands to execute for tusd hooks and FTP watcher commands are located in the same directory ``fuw/hooks``.

### GigaDB Webapp

The legacy web site that serve dataset files and their metadata.
It's built with Yii 1 framework (but it can be coded with Yii2 code).
The web application is fronted by the nginx web and TLS termination server container and it runs inside a php-fpm/php 7.1 container using official Debian Stretch (8) based image.

The automated testing for this webapp make use of three other containers, test container, phantomjs container and a database container.

#### test

#### phantomjs

#### database

### File Upload Wizard Webapps

The user-visible web application for authors to upload, reviewers to audit and curators to administrate the files associated to a newly accepted manuscript. It is built on Yii 2 web framework using Yii2 Advanced project template. It consists of three sub-apps, each running in their container.
File Upload Wizard use the same database server container as Gigadb Webapp, but has a separate database schema.

#### fuw-admin

In Yii2 terminology, this is the backend application. Intended only for admin task, in our case curator's tasks. Because the workflow for curator is triggered using UX in Gigadb Webapp, fuw-admin can be built as a only a REST Api to receive commands from Gigadb Webapp.

#### console

#### fuw-user

### Certificate management

### Console and testing

### Prototype

