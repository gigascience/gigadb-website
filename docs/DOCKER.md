# Installing GigaDB as a multiple Docker container application

GigaDB can be deployed as a multiple container application using Docker. Using
the Docker Compose tool, GigaDB can be deployed for development work using
three containers running NGINX, PHP-FPM and PostgreSQL services.

The deployment of GigaDB with Docker uses the 
[yii2-laradock](https://github.com/ydatech/yii2-laradock) fork from
[Laradock](https://github.com/laradock/laradock) Docker PHP development
framework created by [Mahmoud Zalt](https://github.com/Mahmoudz). 

## Steps

After you have `git clone https://github.com/gigascience/gigadb-website.git`, 
you will downloaded the `gigadb-website` repository. The next step is to 
download the GigaDB-specific Laradock project as a sub-module:
```bash
# Change directory
$ cd gigadb-website
# Change branch
$ git checkout develop
# Download sub-modules
$ git submodule init
$ git submodule update
```

You should see a `yii2-laradock` directory in your `gigadb-website` repository. 
Now create a docker-compose configuration file in the `yii2-laradock` directory:
```bash
$ cp yii2-laradock/env-gigadb yii2-laradock/.env
```

Chef-Solo is used to create a number of website source files from templates. The
values to configure various variables in these template files come from a 
`development.json` file located in the `gigadb-website/chef/environments`
directory. This file can be created by copying the `development.json.sample` 
into a new file called `development.json`:

```bash
cp chef/environments/development.json.sample chef/environments/development.json
```

Ensure the four variables in your development.json below are set as follows:

```bash
{
  "name": "development",
  "default_attributes": {
    "gigadb": {
      "db": {
        "host": "postgres"
      },
      "site_dir": "/var/www",
      "root_dir": "/vagrant",
      "yii_path": "/opt/yii-1.1.16",
```

The values for the other variables in this `development.json` file can be left 
as they are.

Vagrant can now be used to spin up an Ubuntu VM with Docker installed and with 
the `gigadb-website` repository folder synchronised at `/vagrant`:
```bash
$ vagrant up
# Log into Ubuntu VM
$ vagrant ssh
```

If you change directory to `/vagrant/yii2-laradock` in the Ubuntu VM, you can 
use the [Docker Compose](https://docs.docker.com/compose/) tool to build and 
start the separate containers that can collectively run an instance of GigaDB. 
This tool relies on a `docker-compose.yml` file which specifies what services 
are in the GigaDB Docker application.
```bash
$ cd /vagrant/yii2-laradock
$ docker-compose up -d nginx postgres 
```

If this docker-compose process is successful then the GigaDB website will be 
visible at [http://192.168.42.10]( http://192.168.42.10) on your web browser.

## List containers

All of the project containers in this Dockerised version of GigaDB can be 
listed:

```bash
$ docker-compose ps
             Name                          Command              State                     Ports                  
  ---------------------------------------------------------------------------------------------------------------
  yii2laradock_applications_1   /true                           Exit 0                                           
  yii2laradock_nginx_1          nginx                           Up       0.0.0.0:443->443/tcp, 0.0.0.0:80->80/tcp
  yii2laradock_php-fpm_1        docker-php-entrypoint php-fpm   Up       9000/tcp                                
  yii2laradock_postgres_1       docker-entrypoint.sh postgres   Up       0.0.0.0:5432->5432/tcp                  
  yii2laradock_workspace_1      /sbin/my_init                   Up       0.0.0.0:2222->22/tcp    
```

You will see that GigaDB is comprised on 5 containers:

 * **yii2laradock_applications_1** is a small container that has finished running,
 hence its exit state. Its role is to point to the gigadb-website directory to
 allow the source code to be used by the other containers.
 * **yii2laradock_nginx_1** provides a container running the NGINX web server.
 * **yii2laradock_php-fpm_1** is a container which runs a FastCGI server for 
 PHP applications. It parses the PHP code and returns a response.
 * **yii2laradock_postgres_1** is the PostgreSQL container which hosts the
 gigadb database that manages the metadata for the data files in GigaDB.
 * **yii2laradock_workspace_1** provides a container that allows you to run
  artisan and other command line tools when doing development coding for GigaDB.

# Check log for container

```bash
$ docker-compose logs <name of container>
```

# Build images
```bash
$ docker-compose build <name of container>
```

# Delete containers
```bash
$ docker-compose down -v
```

# Log into a  container
```bash
$ docker exec -it yii2laradock_nginx_1 bash
```

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

# pgAdmin

The PostgreSQL database can be managed using the pgAdmin tool. This container
can be deployed using the docker-compose tool:

```bash
$ docker-compose up -d pgadmin
```

Once the pgadmin container is running, it can be accessed from a browser at 
[http://192.168.42.10:5050](http://192.168.42.10:5050).

You will see a login webpage. Enter `pgadmin4@pgadmin.org` as the email address
and `admin` for the password.

To add your database running on the postgres container, click on `Add New 
Server` and provide a new name, e.g. `gigadb`. Click on the connection tab and
input the hostname/address as `192.168.42.10`. Use `gigadb ` as the username and
`vagrant` as the password to access the gigadb postgres database.

