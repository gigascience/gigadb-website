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
chef cookbooks as described in [INSTALL.md](../docs/INSTALL.md). A development.json
file should also be present in the chef/environments directory.

Docker uses Linux-specific tools which means that it cannot run natively on 
Apple or Windows computers. Instead, a Linux VirtualBox VM is used to host and run 
a Docker server. The Vagrantfile contains the instructions for Vagrant to deploy 
a VM running Docker on Ubuntu. This can be done with the command below:

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
located in `/vagrant`. Change directory to this folder:

```bash
[vagrant@localhost ~]$ cd /vagrant
```

The Ubuntu VM already has the Docker software pre-installed which can be
used to run the Dockerfile in the `/vagrant` directory. Create the Docker 
image from this Dockerfile:

```bash
# This cmd will generate a load of status messages
[vagrant@localhost ~]$ sudo docker build -t gigadb .
Sending build context to Docker daemon   58.4MB
Step 1/8 : FROM centos:6.7
6.7: Pulling from library/centos
<snip></snip>
Step 8/8 : CMD ["/bin/bash"]
 ---> Running in 133e56319f09
Removing intermediate container 133e56319f09
 ---> 942d8117ff80
Successfully built 942d8117ff80
Successfully tagged gigadb:latest
# Check image has been created by asking for an image list
[vagrant@localhost ~]$ sudo docker images
REPOSITORY          TAG                 IMAGE ID            CREATED              SIZE
gigadb              latest              942d8117ff80        About a minute ago   367MB
centos              6.7                 000c5746fa52        5 weeks ago          191MB
```

Images can be deleted using the `rmi` command. For example, all images can be
deleted using:

```bash
[vagrant@localhost ~]$ sudo docker rmi $(sudo docker images -q)
```

### Docker containers

To execute the `gigadb` image as a container, use the run command. -p publishes the 
container's ports to the host Ubuntu VM that is running Docker. The -v mounts the host's 
`/vagrant` directory into a given directory in the container, the -it allocates a 
pseudo-TTY connected to the container's standard input, thereby providing an 
interactive bash shell.

```bash
# Expose port 80 from the container onto port 8080 
# in the virtual machine that is running Docker
$ sudo docker run -p 8080:80 --privileged=true -v /vagrant:/vagrant -it gigadb bash
[root@c546b41fc049 /]# 
```

The gigadb container does not have the GigaDB website installed but this 
can be performed using chef-solo:

```bash
/usr/bin/chef-solo -l info -c /vagrant/docker/docker-chef-solo/solo.rb -j /vagrant/docker/docker-chef-solo/node.json
```

The above command will generate a lot of messages informing you about the status
of the Chef-Solo provisioning. When this is complete, you can check the GigaDB 
web site is running by opening a web browser on your host and pointing it to 
[http://192.168.42.10:8080](http://192.168.42.10:8080) which is the IP address 
of the virtual machine that is running Docker. Don't forget that port 8080 on the
Ubuntu VM/Docker host is showing the web page served by NGINX which is running in 
the container.

The use of Docker and Chef to provision a container with the software to run GigaDB
will not provide any significant savings in time in deploying the website for
development purposes. However, it will enable your computer to run the website in
conjunction with Docker containers running a test FTP server and a file preview server 
(in development) with less computational overheads.

## Containers and their IP address

The IP address for a container can be obtained by first getting the container
ID:

```bash
$ sudo docker ps
CONTAINER ID        IMAGE               COMMAND                CREATED             STATUS              PORTS                    NAMES
10a8c0e40017        gigadb              "/run-httpd.sh"   3 seconds ago       Up 2 seconds        0.0.0.0:8080->80/tcp   goofy_sinoussi 
```

The grep command can the be used with the inspect Docker command to get the
container's IP address:

```
$ sudo docker inspect 10a8c0e40017 | grep IPAddress
       "IPAddress": "172.17.0.6",
        "SecondaryIPAddresses": null,
```

## SSH access to containers

You can SSH into containers using `attach`:

```
$ sudo docker attach 8b4cea38f8ab
```

## Deleting containers

To delete a container, it has to be stopped first. For example:

```bash
# Stop all containers
[vagrant@localhost ~]$ sudo docker stop $(sudo docker ps -a -q)
# Containers can then be removed
[vagrant@localhost ~]$ sudo docker rm $(sudo docker ps -a -q)
```
 
