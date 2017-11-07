# Using Docker within a VM from Vagrant

## Background

A Docker container is a lightweight virtualised operating system (OS)
environment that can be deployed to run isolated packages of software in 
a host computer. Unlike a virtual machine, the container does not need a guest 
OS because it shares the kernel of the host OS. The kernel mediates access to 
the computer's CPU, RAM and hardware such as the keyboard, mouse, monitor, etc.

A Docker image acts as a template to create an instance of it in the form of a
Docker container. A Dockerfile provides the source code which details which 
software should be incorporated inside an image. These Dockerfiles allow images 
to be layered one after the another.

## Preparation

Docker uses Linux-specific tools which used to mean that it cannot run
natively on Mac or Windows computers. This means that a VirtualBox VM is used 
to host and run the Docker software. The Vagrantfile contains the instructions
for Vagrant to deploy a VM running Centos 6 and the Docker software and a copy
of all the files in this GitHub repository. This can be done with the command 
below:

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

The files in the gigadb-website GitHub repo have been synced into the VM and are
located in /vagrant. Change directory to folder:

```bash
[vagrant@localhost ~]$ cd /vagrant
```

There is a Dockerfile in this directory. Create the Docker image from this 
Dockerfile:

```bash
[vagrant@localhost ~]$ sudo docker build -t sample .
# Check image has been created by asking for an image list:
[vagrant@localhost ~]$ sudo docker images
REPOSITORY          TAG                 IMAGE ID            CREATED             VIRTUAL SIZE
sample              latest              4a8354e4b795        50 minutes ago      347.3 MB
centos              latest              1c1b67b33c28        5 weeks ago         196.6 MB
```

Images can be deleted using the `rmi` command. For example, all images can be
deleted using:

```bash
[vagrant@localhost ~]$ sudo docker rmi $(sudo docker images -q)
```

### Docker containers

To run the `sample` image as a container, use the run command. -p publishes the 
container's ports to the host, -v mounts the host's /vagrant directory into a 
given directory in the container, and -d runs the `sample` image as a container 
in the background:

```bash
# Expose port 80 from the container onto port 8080 
# in the virtual machine that is running Docker
$ sudo docker run -p 8080:80 -v /vagrant:/vagrant -d sample
# Check container has been created
$ sudo docker ps -a
CONTAINER ID        IMAGE               COMMAND                CREATED             STATUS              PORTS                NAMES
85789746e9d1        sample              "/usr/sbin/apachectl   24 minutes ago      Up 24 minutes       0.0.0.0:80->80/tcp   condescending_engelbart   
```

You can now check the container is running by opening a web browser on your host
and pointing it to [http://192.168.42.10:8080](http://192.168.42.10:8080) which 
is the IP address of the virtual machine. Port 8080 is showing the web page 
served by Apache which is running on the container.

To get the IP address for a container from the host VM, first get the container
ID:

```bash
$ sudo docker ps
CONTAINER ID        IMAGE               COMMAND                CREATED             STATUS              PORTS                    NAMES
10a8c0e40017        sample              "/run-httpd.sh"   3 seconds ago       Up 2 seconds        0.0.0.0:8080->80/tcp   goofy_sinoussi 
# Then use the container ID to execute inspect and grep the IP address
$ sudo docker inspect 10a8c0e40017 | grep IPAddress
       "IPAddress": "172.17.0.6",
        "SecondaryIPAddresses": null,
```

You can now do a further check by downloading the displayed file using the 
container IP address from within the VM that is hosting the Docker container:

```bash
[vagrant@localhost vagrant]$ wget 172.17.0.6
Connecting to 172.17.0.7:80... connected.
HTTP request sent, awaiting response... 200 OK
Length: 13 [text/html]
Saving to: “index.html”

100%[================================================================================================================================>] 13          --.-K/s   in 0s      

2017-11-02 07:14:53 (2.07 MB/s) - “index.html” saved [13/13]
```

To SSH into a Docker container, use the `containerId` or `containerName` that 
you want to connect to:

```bash
[vagrant@localhost ~]$ sudo docker exec -it condescending_engelbart bash
```

To delete a container, it has to be stopped first:

```bash
# Stop all containers
[vagrant@localhost ~]$ sudo docker stop $(sudo docker ps -a -q)
# Remove all containers
[vagrant@localhost ~]$ sudo docker rm $(sudo docker ps -a -q)
```
 
