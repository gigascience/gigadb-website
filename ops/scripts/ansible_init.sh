#!/usr/bin/env bash

set -e

source ../../../../.env
source .init_env_vars

while [[ $# -gt 0 ]]; do
    case "$1" in
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
if [ -z $target_environment ];then
  read -p "You need to specify a target environment (staging or live): " target_environment
fi


# Ensure we are in the environment-specific directory
if [ "envs/$target_environment" != `pwd | rev | cut -d"/" -f 1,2 | rev` ];then
  echo "You are not in the correct directory given the specified parameters. you should be in 'envs/$target_environment'"
  exit 1
fi

# copy files into the environment specific directory
cp ../../playbook.yml .



# Update properties file with values from GitLab so Ansible can configure the services
gigadb_db_host=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_host?filter%5benvironment_scope%5d=$target_environment" | jq .value)
gigadb_db_user=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_user?filter%5benvironment_scope%5d=$target_environment" | jq .value)
gigadb_db_password=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_password?filter%5benvironment_scope%5d=$target_environment" | jq .value)
gigadb_db_database=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_database?filter%5benvironment_scope%5d=$target_environment" | jq .value)

echo "gigadb_db_host = $gigadb_db_host" > ansible.properties
echo "gigadb_db_user = $gigadb_db_user" >> ansible.properties
echo "gigadb_db_password = $gigadb_db_password" >> ansible.properties
echo "gigadb_db_database = $gigadb_db_database" >> ansible.properties

fuw_db_host=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/fuw_db_host?filter%5benvironment_scope%5d=$target_environment" | jq .value)
fuw_db_user=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/fuw_db_user?filter%5benvironment_scope%5d=$target_environment" | jq .value)
fuw_db_password=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/fuw_db_password?filter%5benvironment_scope%5d=$target_environment" | jq .value)
fuw_db_database=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/fuw_db_database?filter%5benvironment_scope%5d=$target_environment" | jq .value)

echo "fuw_db_host = $fuw_db_host" >> ansible.properties
echo "fuw_db_user = $fuw_db_user" >> ansible.properties
echo "fuw_db_password = $fuw_db_password" >> ansible.properties
echo "fuw_db_database = $fuw_db_database" >> ansible.properties

echo "deployment_target = $deployment_target" >> ansible.properties
echo "gitlab_project = $gitlab_project" >> ansible.properties
echo "ssh_private_key_file = $aws_ssh_key" >> ansible.properties
echo "gitlab_private_token= $GITLAB_PRIVATE_TOKEN" >> ansible.properties

