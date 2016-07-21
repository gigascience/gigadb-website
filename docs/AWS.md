# Deploying GigaDB on AWS

The GigaDB website can be deployed on an AWS EC2 instance using Vagrant.
This document outlines how this can be achieved.

## Preparation

In addition to downloading the gigadb-website GitHub source code
repository and its chef-cookbooks submodule, an AWS user account is 
required to launch an EC2 instance hosting GigaDB. Vagrant needs to be 
able to access the AWS user account's security credentials using the 
following environment variables:

AWS_ACCESS_KEY_ID="Access key for accessing AWS"
AWS_SECRET_ACCESS_KEY="Secret access key for accessing AWS"
AWS_DEFAULT_REGION="The region to start the instance in"
AWS_KEYPAIR_NAME="Name of keypair used to bootstrap AMIs"
AWS_SECURITY_GROUPS="Name of AWS security group to use"
AWS_SSH_PRIVATE_KEY_PATH="Path to AWS private key"
AWS_KEYPAIR_NAME="Name of keypair used to bootstrap AMIs"

The above environment variables can be set and managed in your
.bash_profile file.

In order for Vagrant to control and provision machines hosted on EC2
instances, the AWS provider plugin for Vagrant needs to be installed:

```bash
$ vagrant plugin install vagrant-aws
```

## Deployment

GigaDB can be deployed by issuing the command below:

```bash
$ vagrant up --provider=aws
```

If successful, the GigaDB website will be visible on a web browser
pointing to the IP address of the EC2 instance.

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