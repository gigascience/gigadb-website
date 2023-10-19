#!/usr/bin/env bash

set -e

# default for EC2 types
web_ec2_type="t3.micro"
bastion_ec2_type="t3.micro"
rds_ec2_type="t3.micro"

source ../../../../.env

if [ -f .init_env_vars ];then
  source .init_env_vars
fi

call_str="$0 $*"

while [[ $# -gt 0 ]]; do
    case "$1" in
    --project)
        has_project=true
        gitlab_project=$2
        shift 2
        ;;
    --ssh-key)
        has_ssh_key=true
        aws_ssh_key=$2
        shift 2
        ;;
    --env)
        has_env=true
        target_environment=$2
        shift 2
        ;;
    --backup-file)
        has_backup_file=true
        backup_file=$2
        shift
        ;;
    --region)
        AWS_REGION=$2
        shift 2
        ;;
    --web-ec2-type)
        web_ec2_type=$2
        shift 2
        ;;
    --bastion-ec2-type)
        bastion_ec2_type=$2
        shift 2
        ;;
    --rds-ec2-type)
        rds_ec2_type=$2
        shift 2
        ;;
    --restore-backup)
        has_restore_backup=true
        ;;
    *)
        echo "Invalid option: $1 in"
        echo $call_str
        exit 1
        ;;
    esac
done

# Ensure variables are not empty
if [ -z $gitlab_project ];then
  read -p "You need to specify a fully qualified Gitlab project (e.g: gigascience/upstream/gigadb-website): " gitlab_project
fi

if [ -z $aws_ssh_key ];then
  read -p "You need to specify the path to the ssh private key to use to connect to the EC2 instance: " aws_ssh_key
fi

if [ -z $target_environment ];then
  read -p "You need to specify a target environment (staging or live): " target_environment
fi

if [ -z $GITLAB_USERNAME ];then
  read -p "You need to specify your GitLab username: " GITLAB_USERNAME
fi

if [ -z $GITLAB_PRIVATE_TOKEN ];then
  read -p "You need to specify your GitLab private token: " GITLAB_PRIVATE_TOKEN
fi


if [ -z $AWS_REGION ];then
  read -p "You need to specify an AWS region: " AWS_REGION
fi

# Output values and ask for confirmation

echo ""
echo "Current directory: $(pwd)"
echo "Project: $gitlab_project"
echo "Environment: $target_environment"
echo "Region: $AWS_REGION"
echo "GitLab User: $GITLAB_USERNAME"
echo "Web EC2 Type: $web_ec2_type"
echo "Bastion EC2 Type: $bastion_ec2_type"
echo "RDS EC2 Type: $rds_ec2_type"
echo ""

read -p "Do you want to continue (y/n)?" choice
case "$choice" in 
  y|Y ) 
    echo "yes"
    ;;
  n|N ) 
    echo "no"
    exit 0
    ;;
  * ) 
    echo "invalid"
    ;;
esac

# RDS backup restoration requires null restore_to_point_in_time variable in
# terraform.tf to be overridden with real config code block in override.tf
if [ "$has_restore_backup" = true ];then
  cp ../../override.tf .
fi

# url encode gitlab project
encoded_gitlab_project=$(echo $gitlab_project | sed -e 's/\//%2F/g')


# Ensure we are in the environment-specific directory
if [ "envs/$target_environment" != `pwd | rev | cut -d"/" -f 1,2 | rev` ];then
  echo "You are not in the correct directory given the specified parameters. you should be in 'envs/$target_environment'"
  exit 1
fi

# copy the terraform and playbook files to the environment specific directory

cp ../../terraform.tf .
cp ../../getIAMUserNameToJSON.sh .

# Infer the name the EC2 key pair from the file name without extension from teh $aws_ssh_key variable
key_name=$(echo $aws_ssh_key | rev | cut -d"/" -f 1 | rev | cut -d"." -f 1)

# create the terraform variables file (must be named terraform.tfvars for terraform to recognise it automatically)
echo "deployment_target = \"$target_environment\"" > terraform.tfvars
echo "key_name = \"$key_name\"" >> terraform.tfvars
echo "aws_region = \"$AWS_REGION\"" >> terraform.tfvars
echo "web_ec2_type = \"$web_ec2_type\"" >> terraform.tfvars
echo "bastion_ec2_type = \"$bastion_ec2_type\"" >> terraform.tfvars
echo "rds_ec2_type = \"$rds_ec2_type\"" >> terraform.tfvars
# create an environment variable file for this script and for ansible_init.sh
echo "gitlab_project=$gitlab_project" > .init_env_vars
echo "GITLAB_USERNAME=$GITLAB_USERNAME" >> .init_env_vars
echo "GITLAB_PRIVATE_TOKEN=$GITLAB_PRIVATE_TOKEN" >> .init_env_vars
echo "aws_ssh_key=$aws_ssh_key" >> .init_env_vars
echo "deployment_target=$target_environment" >> .init_env_vars
echo "backup_file=$backup_file" >> .init_env_vars
echo "AWS_REGION=$AWS_REGION" >> .init_env_vars
echo "web_ec2_type=$web_ec2_type" >> .init_env_vars
echo "bastion_ec2_type=$bastion_ec2_type" >> .init_env_vars
echo "rds_ec2_type=$rds_ec2_type" >> .init_env_vars

# Update terraform.tfvars file with values from GitLab so Terraform can configure RDS instance
gigadb_db_database=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_database?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
echo "gigadb_db_database=\"$gigadb_db_database\"" >> terraform.tfvars
gigadb_db_user=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_user?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
echo "gigadb_db_user=\"$gigadb_db_user\"" >> terraform.tfvars
gigadb_db_password=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_password?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
echo "gigadb_db_password=\"$gigadb_db_password\"" >> terraform.tfvars


# Check that if the gitlab project is in the Forks group, it must match .env's $REPO_NAME to avoid overwriting some else remote TF state
if [[ $gitlab_project =~ /forks/ && ! $gitlab_project =~ $REPO_NAME ]];then
  echo "Your project ($gitlab_project) is in Forks group but it doesn't match your repo ($REPO_NAME)"
  exit 1
fi

# Initialise a remote terraform state on GitLab

terraform init \
          -backend-config="address=https://gitlab.com/api/v4/projects/$encoded_gitlab_project/terraform/state/${target_environment}_infra" \
          -backend-config="lock_address=https://gitlab.com/api/v4/projects/$encoded_gitlab_project/terraform/state/${target_environment}_infra/lock" \
          -backend-config="unlock_address=https://gitlab.com/api/v4/projects/$encoded_gitlab_project/terraform/state/${target_environment}_infra/lock" \
          -backend-config="username=$GITLAB_USERNAME" \
          -backend-config="password=$GITLAB_PRIVATE_TOKEN" \
          -backend-config="lock_method=POST" \
          -backend-config="unlock_method=DELETE" \
          -backend-config="retry_wait_min=5"

