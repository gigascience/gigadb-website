# Using Docker within a VM from Vagrant

## Background

A Docker container is a lightweight, virtualised operating system (OS)
environment that can be deployed to run isolated packages of software in 
a host computer. Unlike a virtual machine (VM), the container does not need 
a guest OS because it shares the kernel of the host OS. The kernel mediates 
access to the computer's CPU, RAM and hardware such as the keyboard, mouse, 
monitor, etc.

A Docker image acts as a template to create an instance of it in the form of a
Docker container. A Dockerfile provides the source code which details which 
software should be incorporated inside an image. These Dockerfiles allow images 
to be layered one after the another.

The GigaDB website can be deployed within a Docker container for development.
Documentation on how to do this is provided below.

## Preparation

To begin, download GigaDB's source code repository from GitHub and also its
chef cookbooks as described in [INSTALL.md](./INSTALL.md). A development.json
file should also be present in the chef/environments directory.

Docker uses Linux-specific tools which means that it cannot run natively on 
Apple or Windows computers. Instead, a VirtualBox VM is used to host and run 
the Docker software. The Vagrantfile contains the instructions for Vagrant 
to deploy a VM running Ubuntu and Docker. This can be done with the command below:

```bash
$ vagrant up
```

When the VM has been created, you can SSH into the machine:

```bash
$ vagrant ssh
[vagrant@localhost ~]$ 
```

## Docker usage

### Docker images

The files in the gigadb-website GitHub repo are shared with the VM and are
located in /vagrant. Change directory to this folder:

```bash
[vagrant@localhost ~]$ cd /vagrant
```

There is a Dockerfile in this directory. Create the Docker image from this 
Dockerfile:

```bash
[vagrant@localhost ~]$ sudo docker build -t gigadb .
# Check image has been created by asking for an image list:
[vagrant@localhost ~]$ sudo docker images
REPOSITORY          TAG                 IMAGE ID            CREATED             VIRTUAL SIZE
gigadb              latest              4a8354e4b795        50 minutes ago      347.3 MB
centos              latest              1c1b67b33c28        5 weeks ago         196.6 MB
```

Images can be deleted using the `rmi` command. For example, all images can be
deleted using:

```bash
[vagrant@localhost ~]$ sudo docker rmi $(sudo docker images -q)
```

### Docker containers

To run the `gigadb` image as a container, use the run command. -p publishes the 
container's ports to the host VM that is running Docker. The -v mounts the host's 
/vagrant directory into a given directory in the container, the -it allocates a 
pseudo-TTY connected to the container's standard input, thereby providing an 
interactive bash shell.

```bash
# Expose port 80 from the container onto port 8080 
# in the virtual machine that is running Docker
$ sudo docker run -p 8080:80 --privileged=true -v /vagrant:/vagrant -it -name gigadb bash 
# Check container has been created
$ sudo docker ps -a
CONTAINER ID        IMAGE               COMMAND                CREATED             STATUS              PORTS                NAMES
85789746e9d1        gigadb              "/usr/sbin/apachectl   24 minutes ago      Up 24 minutes       0.0.0.0:80->80/tcp   condescending_engelbart   
```

The gigadb container does not have the GigaDB website installed which 
can be performed using chef-solo:

```bash
/usr/bin/chef-solo -l info -c /vagrant/chef/docker-chef-solo/solo.rb -j /vagrant/chef/docker-chef-solo/node.json
```

When the Chef provisioning is complete, you can check the web site is running
by opening a web browser on your host and pointing it to [http://192.168.42.10:8080](http://192.168.42.10:8080) 
which is the IP address of the virtual machine that is running Docker. Port 
8080 is showing the web page served by NGINX which is running in the container.

The IP address for a container can be obtained by first getting the container
ID:

```bash
$ sudo docker ps
CONTAINER ID        IMAGE               COMMAND                CREATED             STATUS              PORTS                    NAMES
10a8c0e40017        gigadb              "/run-httpd.sh"   3 seconds ago       Up 2 seconds        0.0.0.0:8080->80/tcp   goofy_sinoussi 
# Then use the container ID to execute inspect and grep the IP address
$ sudo docker inspect 10a8c0e40017 | grep IPAddress
       "IPAddress": "172.17.0.6",
        "SecondaryIPAddresses": null,
```

To delete a container, it has to be stopped first:

```bash
# Stop all containers
[vagrant@localhost ~]$ sudo docker stop $(sudo docker ps -a -q)
# Remove all containers
[vagrant@localhost ~]$ sudo docker rm $(sudo docker ps -a -q)
```
 
