# rds-instance

## Procedure for deploying GigaDB application with RDS service

### Prerequisites

* A SSH key pair created using the AWS console
* A `sql/production_like.pgdmp` file that is created by `./up.sh`
* An elastic IP, e.g. `eip-ape1-staging-Peter-gigadb`

### Steps

```
# Go to dir
$ cd <path to>/gigadb-website/ops/infrastructure/envs/staging
# Copy terraform files to staging environment
$ ../../../scripts/tf_init.sh --project gigascience/forks/pli888-gigadb-website --env staging
You need to specify the path to the ssh private key to use to connect to the EC2 instance: ~/.ssh/id-rsa-aws.pem
You need to specify your GitLab username: pli888
You need to specify a backup file created by the files-url-updater tool: ../../../../gigadb/app/tools/files-url-updater/sql/gigadbv3_20210929_v9.3.25.backup
# Provision with Terraform
$ terraform plan  
$ terraform apply
$ terraform refresh

# Copy ansible files
$ ../../../scripts/ansible_init.sh --env staging

# Provision with ansible
$ ansible-playbook -i ../../inventories dockerhost_playbook.yml
$ ansible-playbook -i ../../inventories bastion_playbook.yml
```

* Run `build_staging` step on Gitlab CI/CD pipeline
* Run `sd_gigadb` step on Gitlab CI/CD pipeline

If you browse the GigaDB website on your staging server, you should see that the static web pages are displayed but there are error messages viewing the dataset pages probably due to the `dropcontraints` and `dropindexes` database migration steps executed by the `gigadb-deploy-jobs.yml`. To fix this problem, we restore a `gigadb` database using production data on the RDS instance:

```
# In GitLab, click sd_stop_app button in the pipeline
# Assume a backup pgdmp file has been created using files-url-updater tool

# Find out domain name for RDS instance and IP address of bastion server
$ cd <path to>/gigadb-website/ops/infrastructure/envs/staging
$ terraform output
ec2_bastion_public_ip = "18.162.xxx.xxx"
ec2_private_ip = "10.99.x.xx"
ec2_public_ip = "16.162.xxx.xxx"
rds_instance_address = "rds-server-staging.cfkc0cbc20ii.ap-east-1.rds.amazonaws.com"

$ cd <path to>/gigadb-website

# Copy pgdmp file from your local machine to bastion server
$ sftp -i ~/.ssh/id-rsa-aws.pem  centos@18.162.xxx.xxx
sftp> put sql/gigadbv3_20210901_9.6.22.pgdmp
sftp> exit

# Use bastion server to restore database using pgdmp file - will need your gigadb database password
$ ssh -i ~/.ssh/id-rsa-aws.pem  centos@18.162.xxx.xxx "psql -h rds-server-staging.cfkc0cbc20ii.ap-east-1.rds.amazonaws.com -U gigadb -d postgres -c 'drop database gigadb'"
$ ssh -i ~/.ssh/id-rsa-aws.pem  centos@18.162.xxx.xxx "psql -h rds-server-staging.cfkc0cbc20ii.ap-east-1.rds.amazonaws.com -U gigadb -d postgres -c 'create database gigadb owner gigadb'"
$ ssh -i ~/.ssh/id-rsa-aws.pem  centos@18.162.xxx.xxx 'pg_restore -c --if-exists -h rds-server-staging.cfkc0cbc20ii.ap-east-1.rds.amazonaws.com -d gigadb -U gigadb /home/centos/gigadbv3_20210901_9.6.22.pgdmp'

# In GitLab, click sd_start_app button in pipeline
```

To get terraform to destroy bastion server:
```
$ terraform destroy -target module.ec2_bastion.aws_instance.bastion
```