# Deploying GigaDB on AWS

The GigaDB website can be deployed on an AWS EC2 instance using Vagrant.
This document outlines how this can be achieved.

## Amazon Machine Image

Launching GigaDB on AWS requires an Amazon Machine Image (AMI) which
provides the information required to launch a virtual server in the
cloud. The AMI for GigaDB contains a template for a filesystem 
consisting of the Centos 6 operating system and a number of server
applications.

The [Packer](https://www.packer.io) tool was used for creating the AMI
for GigaDB from a source configuration available from
[GitHub repository](https://github.com/pli888/vagrant-boxes) and issuing
the following commands in its root directory:

```sh
$ cd packer
$ ./build.sh
```

Packer uses a pre-built AMI as the source for building the GigaDB AMI.
This process involves using an AWS account to launch an EC2 instance 
from the source pre-built AMI, provisioning this running machine and 
then creating an AMI from this machine. The GigaDB AMI ID is 
ami-1bfa2b78.

## Preparation

An AWS user account is required to launch an EC2 instance deployed with
GigaDB. Vagrant needs to be able to access the AWS user account's 
security credentials using the following environment variables:

```bash
AWS_ACCESS_KEY_ID="Access key for accessing AWS"
AWS_SECRET_ACCESS_KEY="Secret access key for accessing AWS"
AWS_DEFAULT_REGION="The region to start the instance in"
AWS_KEYPAIR_NAME="Name of keypair used to bootstrap AMIs"
AWS_SECURITY_GROUPS="Name of AWS security group to use"
AWS_SSH_PRIVATE_KEY_PATH="Path to AWS private key"
AWS_KEYPAIR_NAME="Name of keypair used to bootstrap AMIs"
```

The above environment variables can be set and managed in your
.bash_profile file.

In order for Vagrant to control and provision machines hosted on EC2
instances, the AWS provider plugin for Vagrant needs to be installed:

```bash
$ vagrant plugin install vagrant-aws
```

## Deployment

To deploy GigaDB on an AWS virtual server, you need to download the 
gigadb-website GitHub source code repository with its chef-cookbooks 
submodule:

```bash
$ git clone https://github.com/gigascience/gigadb-website.git
$ git submodule init
$ git submodule update
```

GigaDB can then be deployed onto the AWS cloud by issuing the command 
below:

```bash
$ vagrant up --provider=aws
```

If successful, the GigaDB website will be visible on a web browser
using the IP address of the EC2 instance.

## SSH access

The aws Chef recipe in the chef/site-cookbooks directory allows user
accounts to be created on your EC2 cloud server. These user accounts
are created with the user's SSH public key. Its corresponding SSH
private key will therefore allow a SSH connection without a password
being required:

```bash
$ ssh peter@ec2-xx-xxx-xxx-x.ap-southeast-1.compute.amazonaws.com
Last login: Thu Jul 21 06:30:01 2016 from xx.xxx.xxx.xxx
[peter@ip-xxx-xx-xx-xxx ~]$ ls

```

## Server security

The GigaDB AWS instance uses a number of security layers to protect the 
virtual server from network attacks. An [AWS security group](http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/using-network-security.html)
is its first firewall of protection against network attacks. If further 
configuration of the AWS security group is required, this can be done 
via the AWS web-based management console.

The GigaDB EC2 server is provisioned with [iptables](https://wiki.centos.org/HowTos/Network/IPTables)
which acts as another firewall. Changes to iptables to allow connections
from other services will need to be configured within the aws Chef 
recipe.

[Fail2ban](http://www.fail2ban.org/wiki/index.php/Main_Page) is another 
layer of protection added by the aws recipe. Fail2ban scans log files 
used by services such as nginx and bans IP addresses that show malicious
signs of attacking the server, e.g. too many login and password 
failures. These IP addresses are then blocked from connecting to the 
server by updating iptables rules. Which log files are scanned are 
configured by setting banning policies. These policies are listed in the 
aws recipe.

[SElinux](https://wiki.centos.org/HowTos/SELinux) is an access control 
mechanism which has been switched on in the GigaDB EC2 server. Its 
configuration is controlled within the aws recipe.
