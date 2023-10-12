# SOP: Deploying to gigadb.org

A deployment of the GigaDB website code in the `Upstream` Gitlab group to the
`live` environment provides the website that is located at gigadb.org.

The terraform state file is stored remotely in gitlab using terraform `http` backend module,
details of the implementation can be found at [here](https://docs.gitlab.com/ee/user/infrastructure/iac/terraform_state.html).
As a result, the state file can be shared which allows multiple developers to provision the same existing infrastructure.
One important thing to note is that the existing infrastructure can only be provisioned by 1 developer at a time, as the remote state file
will be locked, this help to make sure that each developer only works on the most recent infrastructure.

If you have any problem during the deployment process, please first check the [troubleshooting guide](PRODUCTION_TROUBLESHOOT.md).

## Prerequisites

### AWS IAM policies
[Click to see how to check IAM policies](AWS_SETUP.md#verify-aws-iam-policies)

### Elastic IP addresses for staging and live environments

Both the staging.gigadb.org and gigadb.org domain names have been allocated
with AWS elastic IP addresses in the ap-east-1 region. You can check the current 
elastic IP addresses pointing in the AWS EC2 console:

1. Go to the [cnhk-infra CI/CD variables page](https://gitlab.com/gigascience/cnhk-infra/-/settings/ci_cd) 
to get the password for the `Gigadb` AWS IAM user.
2. Use the `Gigadb` AWS IAM user credentials to log into the AWS console.
3. Go to the [Elastic IP addresses page](https://ap-east-1.console.aws.amazon.com/ec2/v2/home?region=ap-east-1#Addresses:)
4. Check there is an `eip-gigadb-live-gigadb` and an `eip-gigadb-staging-gigadb` 
elastic IP address.

### Domain name resolution to staging.gigadb.org and gigadb.org

Resolution to staging and gigadb.org with the above elastic IP addresses 
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
for the Host with a value equal to the `eip-gigadb-live-gigadb` elastic 
IP address. There will also be an entry for the `staging` Host too.

### Set up credentials for accessing AWS resources
[Click to see how to setup credentials](AWS_SETUP.md#set-up-credentials-for-accessing-aws-resources)

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

And you have to make sure that terraform state files exist in [gitlab terraform page](https://gitlab.com/gigascience/upstream/gigadb-website/-/terraform).
You can click to download the [shared Terraform state for Upstream's staging environment](https://gitlab.com/api/v4/projects/11385199/terraform/state/staging_infra/versions/144)
for checking in the latter staging deployment steps

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
# this step will retrieve terraform state file from the gitlab
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

And you should use Gigadb AWS IAM user account to provision production staging server:
```
$ terraform show # will show all the existing resources, which should be the same as the terraform state file `staging_infra` from gitlab.
$ terraform plan # terraform is idempotent and should not try to create new instances for already existing upstream staging, unless the new instance is expected to create.
$ terraform apply # will make changes to the existing infrastructure and update the terraform state file, input `yest` if the changes are expected to make.
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
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=staging"
```

Provision web application server:
```
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=staging"
```

Provision monitoring server:
```
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories monitoring_playbook.yml
```

Additional features for executing ansible playbooks:
```
# display all availale plays
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=staging" --list-tags
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=staging" --list-tags
# execute selected plays
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=staging" --tags files-url-updater,rclone-tool
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=staging" --tags setup-docker-ce
# skip selected plays
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "backupDate=latest" --extra-vars="gigadb_env=staging" --skip-tags fix-centos-eol-issues,setup-fail2ban,setup-docker-ce,restore-db-on-rds,load-latest-db
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=staging" --skip-tags fix-centos-eol-issues,setup-fail2ban
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
and run all the jobs in the staging build stage in your pipeline, including but are not limited to `build_staging`, `TidewaysBuildStaging`.

2. Next, run all the jobs in the staging deploy stage in your pipeline, including but are not limited to `sd_gigadb`, `TidewaysDeployStaging`.

## Provision AWS infrastructure for `live` environment using Terraform and Ansible

You have to make sure there is no error when deploying `staging` environment.
Then, you need to follow the exact steps in the staging provision section and look into the details of
`terrafor show` and `terraform plan` commands.

And also, you can click to download the [shared Terraform state for Upstream's live environment](https://gitlab.com/api/v4/projects/11385199/terraform/state/live_infra/versions/76)
for checking in the latter live deployment steps.

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
$ terraform show # will show all the existing resources, which should be the same as the terraform state file `live_infra` from gitlab.
$ terraform plan # terraform is idempotent and should not try to create new instances for already existing upstream staging, unless the new instance is expected to create.
$ terraform apply # will make changes to the existing infrastructure and update the terraform state file, input `yest` if the changes are expected to make.
$ terraform refresh
```

Copy ansible files into `live` environment:
```
$ ../../../scripts/ansible_init.sh --env live
```

Install third party Ansible roles:
```
$ ansible-galaxy install -r ../../../infrastructure/requirements.yml
```

Provision RDS via bastion server:
```
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=staging"
```

Enable cronjob for periodically resetting the database:
```
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "reset_database_cronjob_state=present" --extra-vars="gigadb_env=staging"
```

Enable cronjob for periodically creating a database dump and storing it in S3:
```
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "upload_database_backup_to_S3_cronjob_state=present" --extra-vars="gigadb_env=staging"
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
$ TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=staging"
```

Provision monitoring server:
```
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories monitoring_playbook.yml
```

Additional features for executing ansible playbooks:
```
# display all availale plays
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=live" --list-tags
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=live" --list-tags
# execute selected plays
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=live" --tags files-url-updater,rclone-tool
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=live" --tags setup-docker-ce
# skip selected plays
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "backupDate=latest" --extra-vars="gigadb_env=live" --skip-tags fix-centos-eol-issues,setup-fail2ban,setup-docker-ce,restore-db-on-rds,load-latest-db
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=live" --skip-tags fix-centos-eol-issues,setup-fail2ban
```

## Deploy to gigadb.org using CI/CD pipeline

1. Go to [Gitlab Upstream pipeline page](https://gitlab.com/gigascience/upstream/gigadb-website/-/pipelines)
and run all the jobs in the live build stage in your pipeline, including but are not limited to `build_live`, `TidewaysBuildLive`.

2. Next, run all the jobs in the live deploy stage in your pipeline, including but are not limited to `ld_gigadb`, `TidewaysDeployLive`.
