# Deploying GigaDB on AWS

The GigaDB website can be deployed on an AWS EC2 instance using Vagrant.
This document outlines how this can be achieved.

## Amazon Machine Image

Launching GigaDB on AWS requires an Amazon Machine Image (AMI) which
provides the information required to launch a virtual server in the
cloud. The AMI for GigaDB contains a template for the root volume which
consists of the Centos 6 operating system and a number of server
applications, launch permissions which control which AWS accounts can 
use the AMI to launch instances and a block device mapping that 
specifies the volumes to attach to the instance when it is launched.

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
then creating an AMI from this machine. Once created, the AMI was 
uploaded into AWS and available for use with the AMI ID: ami-1bfa2b78.

## Preparation

An AWS user account is required to launch an EC2 instance hosting
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

GigaDB can be deployed onto the AWS cloud by issuing the command below:

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