#!/usr/bin/env bash

set -e

source ../../../../.env

while [[ $# -gt 0 ]]; do
    case "$1" in
    --project)
        has_project=true
        gitlab_project=$2
        shift
        ;;
    --env)
        has_env=true
        target_environment=$2
        shift
        ;;
    *)
        echo "Invalid option: $1"
        exit 1  ## Could be optional.
        ;;
    esac
    shift
done

# Ensure variables are not empty
if [ -z $gitlab_project ];then
  read -p "You need to specify a fully qualified Gitlab project (e.g: gigascience%2Fupstream%2Fgigadb-website): " gitlab_project
fi

if [ -z $target_environment ];then
  read -p "You need to specify a target environment (staging or live): " target_environment
fi

if [ -z $GITLAB_USERNAME ];then
  read -p "You need to specify your GitLab username: " GITLAB_USERNAME
fi

if [ -z $GITLAB_PRIVATE_TOKEN ];then
  read -p "You need to specify your GitLab username: " GITLAB_PRIVATE_TOKEN
fi


# Ensure we are in the environment-specific directory
if [ "envs/$target_environment" != `pwd | rev | cut -d"/" -f 1,2 | rev` ];then
  echo "You are not in the correct directory given the specified parameters. you should be in 'envs/$target_environment'"
  exit 1
fi

# copy the terraform and playbook files to the environment specific directory

cp ../../terraform.tf .
cp ../../getIAMUserNameToJSON.sh .
cp ../../playbook.yml .

# create the environment variable file (must be named terraform.tfvars for terraform to recognise it automatically)
echo "deployment_target = \"$target_environment\"" > terraform.tfvars

# Initialise a remote terraform state on GitLab

terraform init \
          -backend-config="address=https://gitlab.com/api/v4/projects/$gitlab_project/terraform/state/${target_environment}_infra" \
          -backend-config="lock_address=https://gitlab.com/api/v4/projects/$gitlab_project/terraform/state/${target_environment}_infra/lock" \
          -backend-config="unlock_address=https://gitlab.com/api/v4/projects/$gitlab_project/terraform/state/${target_environment}_infra/lock" \
          -backend-config="username=$GITLAB_USERNAME" \
          -backend-config="password=$GITLAB_PRIVATE_TOKEN" \
          -backend-config="lock_method=POST" \
          -backend-config="unlock_method=DELETE" \
          -backend-config="retry_wait_min=5"


