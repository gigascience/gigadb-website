# SOP: Deploying to beta.gigadb.org

A deployment of the GigaDB website code in the `Upstream` Gitlab group to the
`live` environment provides the website that is located at beta.gigadb.org.

## Prerequisites

### AWS IAM policies

The `Gigadb` user needs the same AWS IAM policy permissions for accessing EC2, 
RDS and S3 services as the developers. This can be checked by viewing the AWS 
[IAM dashboard](https://us-east-1.console.aws.amazon.com/iamv2/home?region=us-east-1#/home).
The `Gigadb` user has been added to the `Applications` IAM group. This group
has the same permissions as those used by the developers.

> :warning: **You'll need your AWS admin user account to access the IAM console**

### Elastic IP addresses for staging and live environments

Both the staging.gigadb.org and beta.gigadb.org domain names have been allocated
with AWS elastic IP addresses in the ap-east-1 region. You can check the current 
elastic IP addresses pointing in the AWS EC2 console:

1. Go to the [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd) 
to get the password for the `Gigadb` AWS IAM user.
2. Use the `Gigadb` AWS IAM user credentials to log into the AWS console.
3. Go to the [Elastic IP addresses page](https://ap-east-1.console.aws.amazon.com/ec2/v2/home?region=ap-east-1#Addresses:)
4. Check there is an `eip-gigadb-live-gigadb` and an `eip-gigadb-staging-gigadb` 
elastic IP address.

### Domain name resolution to staging.gigadb.org and beta.gigadb.org

Resolution to staging and beta.gigadb.org with the above elastic IP addresses 
require DNS A records. Check these records have been created in Alibaba Cloud 
console:

1. Go to the [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd)
to get the `Alibaba_user_email` and `Alibaba_user_password`.
2. Log into the [Alibaba Cloud console](https://account.alibabacloud.com/login/login.htm?oauth_callback=https%3A%2F%2Fhome-intl.console.aliyun.com%2F%3Fspm%3Da3c0i.7911826.6791778070.41.44193870AxVzyk&lang=en) using the above credentials.
3. You will be asked for a 6 digit one time password (OTP) that is provided by 
the Google Authenticator app.
> :warning: **You'll need to contact pli888 for the OTP**
4. Once logged into the console, go to the Domain Names page
5. You will see an entry for `gigadb.org` domain - click on this
6. You will now see the `DNS Settings gigadb.org` page. There should be an entry
for the `beta` Host with a value equal to the `eip-gigadb-live-gigadb` elastic 
IP address. There will also be an entry for the `staging` Host too.

### Set up credentials for accessing AWS resources

> :warning: **Mistakes can happen with interchanging between AWS configurations below**

1. Save `id-rsa-aws-hk-gigadb.pem` available from [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd) into your `~/.ssh` directory.
2. Create a `known_hosts` file in `~/.ssh` if it does not already exist
```
$ touch ~/.ssh/known_hosts
```
3. Copy your `~/.aws/config` file into a new file:
```
$ cd ~/.aws
$ cp config config.ap-northeast-1
# Use this if you are based in France
$ cp config config.eu-west-3
```
3. Create an AWS config file to use for deploying to staging or 
beta.gigadb.org:
```
$ vi config.upstream
# The contents of config.upstream:
[default]
region=ap-east-1
output=json

[profile Gigadb]
region=ap-east-1
output=json
```
4. The staging and beta.gigadb.org websites are deployed in the ap-east-1 Hong
Kong regions. You will need to copy `config.upstream` to a new `config` file to
do this:
```
$ cp config.upstream config
```
> :warning: **You will need to overwrite the upstream `config` file with `config.ap-northeast-1` when returning to your development work**

5. Copy your `~/.aws/credentials` file into a new file:
```
$ cp credentials credentials.ap-northeast-1
# Use this if you are based in France
$ cp credentials credentials.eu-west-3
```

6. Create an AWS credentials file for deploying to staging or beta.gigadb.org:
```
$ vi credentials.upstream
# The contents of credentials.upstream:
[default]
aws_access_key_id=<aws_access_key_id for Gigadb user>
aws_secret_access_key=<aws_secret_access_key for Gigadb user>

[Gigadb]
aws_access_key_id=<aws_access_key_id for Gigadb user>
aws_secret_access_key=<aws_secret_access_key for Gigadb user>
```
7. Overwrite your current credentials file with the contents of 
`credentials.upstream`:
```
$ cp credentials.upstream credentials
```
> :warning: **You will need to overwrite the upstream `credentials` file with `credentials.ap-northeast-1` when returning to your development work**

Another option is to create a new `Gigadb` user account in your operating system
and only setting up the `Gigadb` AWS user configuration in it. This means you
will use this `Gigadb` operating system user for managing deployments to 
staging and beta.gigadb.org.

### Tools

ensure gnu-tar is installed on the system you will be running the provisioning script:
```
$ brew install gnu-tar
```

## Deployment procedure

### Set up gigadb-website repo on your local dev environment

In your `PhpstormProjects` folder, create a new directory called `gigascience`:
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

Check you are tracking the `origin` repository:
```
$ git remote -v
origin  https://github.com/gigascience/gigadb-website.git (fetch)
origin  https://github.com/gigascience/gigadb-website.git (push)
```

Switch to the `develop` branch:
```
$ git checkout develop
```

Check you have the latest code for the `develop` branch:
```
$ git log
```

If not, then get the latest code:
```
$ git fetch origin
$ git rebase origin/develop
```

Check you can deploy GigaDB in your `dev` environment:
```
# Create .env file
$ cp ops/configuration/variables/env-sample .env

# Edit .env file with particular focus on the variables below:
GITLAB_PRIVATE_TOKEN=<your_gitlab_private_token>

REPO_NAME="gigadb-website"
GROUP_VARIABLES_URL="https://gitlab.com/api/v4/groups/gigascience/variables?per_page=100"
FORK_VARIABLES_URL="https://gitlab.com/api/v4/groups/3506500/variables"
PROJECT_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fupstream%2F$REPO_NAME/variables"

# Execute up.sh
$ ./up.sh
```

Check your `dev` GigaDB website is available at http://gigadb.gigasciencejournal.com:9170 
in your browser.

### Provision AWS infrastructure for `staging.gigadb.org` using Terraform and Ansible

Ensure you have a database back up file that Ansible can use to restore a
PostgreSQL database on the AWS Relational Database Service. For example, copy
a file from your `dev` gigadb-website repo:
```
$ cp <path_to>/pli888/gigadb-website/gigadb/app/tools/files-url-updater/sql/gigadbv3_20210929_v9.3.25.backup <path_to>/gigascience/gigadb-website/gigadb/app/tools/files-url-updater/sql
```

Before you are able to create a `live` deployment, you must first deploy a 
`staging` environment.

Change directory to the `envs` folder:
```
$ cd ops/infrastructure/envs
```

Change directory to the `staging` folder:
```
$ cd staging
```

Copy terraform files to `staging` environment:
```
$ ../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env staging

You need to specify the path to the ssh private key to use to connect to the EC2 instance: 
~/.ssh/id-rsa-aws-hk-gigadb.pem

You need to specify your GitLab username:
pli888 | rija | kencho51

You need to specify a backup file created by the files-url-updater tool:
../../../../gigadb/app/tools/files-url-updater/sql/gigadbv3_20210929_v9.3.25.backup

You need to specify an AWS region:
ap-east-1
```

Use Gigadb AWS IAM user account to provision production staging server:
```
$ terraform plan
$ terraform apply
$ terraform refresh
```

Copy ansible files into `staging` environment:
```
$ ../../../scripts/ansible_init.sh --env staging
```

Install third party Ansible roles:
```
$ ansible-galaxy install -r ../../../infrastructure/requirements.yml
```

Provision RDS via bastion server:
```
$ OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml
```

Provision web application server:
```
$ TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml
```

## Deploy to staging.gigadb.org using CI/CD pipeline

### Prerequisites

You need to have permission to push or merge to the protected `develop` branch
in the `Upstream` group in order to run manual jobs such as `build_staging` in 
the CI/CD pipeline otherwise you will see a `You are not authorised to run this
manual job` message. To see who can push/merge, go to repository settings and 
expand the Protected branches section for the gigadb-website project.

### Procedure

1. Go to [Gitlab Upstream pipeline page](https://gitlab.com/gigascience/upstream/gigadb-website/-/pipelines)
and run the staging build stage in your pipeline.

2. Next, run the staging deploy stage in your pipeline

## Provision AWS infrastructure for `live` environment using Terraform and Ansible

Change directory to the `envs` folder:
```
$ cd ops/infrastructure/envs
```

Change directory to the `live` directory:
```
$ cd live
```

Copy terraform files to `live` environment:
```
$ ../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env live

You need to specify an AWS region:
ap-east-1

You need to specify the path to the ssh private key to use to connect to the EC2 instance: 
~/.ssh/id-rsa-aws-hk-gigadb.pem

You need to specify your GitLab username:
pli888 | rijam | kencho51

You need to specify a backup file created by the files-url-updater tool:
../../../../gigadb/app/tools/files-url-updater/sql/gigadbv3_20210929_v9.3.25.backup
```

Use Gigadb AWS IAM user account to provision production staging server:
```
$ terraform plan
$ terraform apply
$ terraform refresh
```

Copy ansible files into `live` environment:
```
$ ../../../scripts/ansible_init.sh --env live
```

Provision RDS via bastion server:
```
$ OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml
```

Enable cronjob for periodically resetting the database:
```
$ OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "reset_database_cronjob_state=present"
```

Enable cronjob for periodically creating a database dump and storing it in S3:
```
$ OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "upload_database_backup_to_S3_cronjob_state=present"
```

To confirm the cron jobs have been created, log in with ssh on bastion, and run 
`crontab -l` - there should be a cronjob to reset the database daily at 10:05am 
UTC (after the CNGB backup of day before is made available).
```
# Get public ip of bastion server
$ terraform output

# ssh into it
$ ssh -i ~/.ssh/id-rsa-aws-hk-gigadb.pem centos@<bastion public ip>
```

Provision web application server:
```
$ TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml
```

## Deploy to beta.gigadb.org using CI/CD pipeline

1. Go to [Gitlab Upstream pipeline page](https://gitlab.com/gigascience/upstream/gigadb-website/-/pipelines)
and run the live build stage in your pipeline.

2. Next, run the live deploy stage in your pipeline
