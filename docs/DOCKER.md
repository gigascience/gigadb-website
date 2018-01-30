# Installing GigaDB as a multiple Docker container application

GigaDB can be deployed as a multiple container application using Docker. Using
the Docker Compose tool, a development deployment of GigaDB can be created using
3 containers running NGINX, PHP-FPM and PostgreSQL services.

## Steps

The deployment of GigaDB with Docker uses the Laradock Docker PHP development
framework created by [Mahmoud Zalt](https://github.com/Mahmoudz). From the
gigadb-website root directory, download the Laradock project:
```bash
$ git submodule init
$ git submodule update
```

You should see a yii2-laradock directory in your gigadb-website repository. Now
create a docker-compose configuration file in the yii2-laradock directory:
```bash
$ cp yii2-laradock/env-gigadb yii2-laradock/.env
```

Vagrant can now be used to spin up an Ubuntu VM with Docker installed and with 
the gigadb-website repository folder synchronised at `/vagrant`:
```bash
$ vagrant up
# Log into Ubuntu VM
$ vagrant ssh
```

If you change directory to `/vagrant/yii2-laradock` in the Ubuntu VM, you can 
now use the Docker Compose tool to build and start separate containers for 
nginx, postgres and pgadmin. This tool relies on a docker-compose.yml file which
specifies what services are in the gigadb-website Docker application.
```bash
$ cd /vagrant/yii2-laradock
$ docker-compose up -d nginx postgres pgadmin
# Might need to run this again because it exits!
$ docker-compose up -d postgres
```

If the docker-compose build and container start process is successful then the 
gigadb-website should be visible at [http://192.168.42.10]( http://192.168.42.10).

# Delete containers and images
```bash
docker-compose down -v
```

# Test nginx container
docker exec -it yii2laradock_nginx_1 bash

# Host access to Docker on Ubuntu VM

You can access the Docker daemon that is running on the Ubuntu VM directly from
your (host) computer without logging into the VM. For example, you can execute
the following command when the Ubuntu VM is deployed:

```bash
$ docker -H tcp://0.0.0.0:9172 version
Client:
 Version:      17.05.0-ce
 API version:  1.29
 Go version:   go1.9.2
 Git commit:   89658be
 Built:        
 OS/Arch:      darwin/amd64

Server:
 Version:      17.12.0-ce
 API version:  1.35 (minimum version 1.12)
 Go version:   go1.9.2
 Git commit:   c97c6d6
 Built:        Wed Dec 27 20:09:53 2017
 OS/Arch:      linux/amd64
 Experimental: false

```

