# SOP: Deploying to beta.gigadb.org

A deployment of the GigaDB website code in the `Upstream` Gitlab group to the
`live` environment provides the website that is located at beta.gigadb.org.

## Check elastic IP address for live environment

The beta.gigadb.org domain name has been allocated with an AWS elastic IP 
address. You can check the current elastic IP address pointing to 
beta.gigadb.org from the AWS EC2 console:

1. Go to the [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd) 
to get the password for the `Gigadb` AWS IAM user.
2. Use the `Gigadb` AWS IAM user credentials to log into the AWS console.
3. Go to the [Elastic IP addresses page](https://ap-east-1.console.aws.amazon.com/ec2/v2/home?region=ap-east-1#Addresses:)
4. Check there is an elastic IP address with the Name, `eip-gigadb-live-gigadb`.

## Check domain name resolution to beta.gigadb.org

Resolution to beta.gigadb.org with the `eip-gigadb-live-gigadb` elastic IP 
address requires a DNS A record. Check this has been created in Alibaba Cloud
console:

1. Go to the [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd)
to get the `Alibaba_user_email` and `Alibaba_user_password` credentials.
2. Log into the [Alibaba Cloud console](https://account.alibabacloud.com/login/login.htm?oauth_callback=https%3A%2F%2Fhome-intl.console.aliyun.com%2F%3Fspm%3Da3c0i.7911826.6791778070.41.44193870AxVzyk&lang=en) using the above credentials.
3. You will be asked for a 6 digit number that is provided by the linked
Google Authenticator app.
4. Once logged into the console, go to the Domain Names page
5. You will see an entry for `gigadb.org` domain - click on this
6. You will now see the `DNS Settings gigadb.org` page. There should be an entry
for the `beta` Host with a value equal to the `eip-gigadb-live-gigadb` elastic 
IP address.
7. If there is no `beta` Host then this DNS A record should be created.

## Set up credentials for accessing AWS resources

> :warning: **Might be worth creating a user account called `Gigadb` on your operating system**

1. Save `id-rsa-aws-hk-gigadb.pem` available from [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd) into your `~/.ssh` directory.
2. In your `~/.aws/credentials` file, make sure there is the following
configuration:
```
[default]
aws_access_key_id=<Gigadb user aws_access_key_id>
aws_secret_access_key=<Gigadb user aws_secret_access_key>

[Gigadb]
aws_access_key_id=<Gigadb user aws_access_key_id>
aws_secret_access_key=<Gigadb user aws_secret_access_key>
```
3. In your `~/.aws/config` file, make sure there is the following configuration:
```
[default]
region=ap-east-1
output=json
 
[profile Gigadb]
region=ap-east-1
output=json
```

## Provision AWS infrastructure using Terraform and Ansible

The set of Terraform and Ansible scripts will create the bastion and web 
application EC2 servers, and a single RDS instance.

In your `PhpstormProjects` folder, create a new directory called 
`gigascience`:
```
# Represents `gigascience` Github user account as opposed to your own Github user account
$ mkdir gigascience
```
Change directory to the `gigascience` folder:
```
$ cd gigascience
```
Download the gigascience/gigadb-website repo from Github:
```
$ git clone https://github.com/gigascience/gigadb-website.git
```
Change directory into the repo:
```
$ cd gigadb-website
```
Change directory to the `envs` folder:
```
$ cd ops/infrastructure/envs
```
Create the `live` directory:
```
$ mkdir live
```
Copy terraform files to `live` environment:
```
$ ../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env live

You need to specify an AWS region:
ap-east-1

You need to specify the path to the ssh private key to use to connect to the EC2 instance: 
~/.ssh/id-rsa-aws-hk-gigadb.pem

You need to specify your GitLab username:
pli888 | rija | kencho51

You need to specify a backup file created by the files-url-updater tool:
../../../../gigadb/app/tools/files-url-updater/sql/gigadbv3_20210929_v9.3.25.backup
```

Provision with Terraform:
```
$ terraform plan
$ terraform apply
$ terraform refresh
```

Use Gigadb AWS IAM user account to provision production staging / live servers:
```
$ AWS_PROFILE=Gigadb terraform plan
$ AWS_PROFILE=Gigadb terraform apply
$ AWS_PROFILE=Gigadb terraform refresh
```

Copy ansible files into `live` environment:
```
$ ../../../scripts/ansible_init.sh --env live
```

Provision RDS via bastion server:
```
$ ansible-playbook -i ../../inventories bastion_playbook.yml
```

Provision web application server:
```
$ TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories webapp_playbook.yml
```

Go to [Gitlab Upstream pipeline page](https://gitlab.com/gigascience/upstream/gigadb-website/-/pipelines)
and run pipeline.