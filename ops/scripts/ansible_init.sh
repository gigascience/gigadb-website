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
cp ../../webapp_playbook.yml .
cp ../../bastion_playbook.yml .
cp ../../users_playbook.yml .
cp ../../monitoring_playbook.yml .

# Update Gitlab gigadb_db_host variable with RDS instance address from terraform-inventory
rds_inst_addr=$(../../inventories/terraform-inventory.sh --list ./ | jq -r '.all.vars.rds_instance_address')
curl -s --request PUT --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_host?filter%5benvironment_scope%5d=$target_environment" --form "value=$rds_inst_addr"
# TODO: uncomment the line below when gigareview is built and deployed through Gitlab Pipeline
#curl -s --request PUT --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/REVIEW_DB_HOST?filter%5benvironment_scope%5d=$target_environment" --form "value=$rds_inst_addr"

# Update properties file with values from GitLab so Ansible can configure the services
gigadb_db_host=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_host?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
gigadb_db_user=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_user?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
gigadb_db_password=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_password?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
gigadb_db_database=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/gigadb_db_database?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)

echo "gigadb_db_host = $gigadb_db_host" > ansible.properties
echo "gigadb_db_user = $gigadb_db_user" >> ansible.properties
echo "gigadb_db_password = $gigadb_db_password" >> ansible.properties
echo "gigadb_db_database = $gigadb_db_database" >> ansible.properties
echo "backup_file = $backup_file" >> ansible.properties

fuw_db_host=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/fuw_db_host?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
fuw_db_user=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/fuw_db_user?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
fuw_db_password=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/fuw_db_password?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
fuw_db_database=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/fuw_db_database?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)

echo "fuw_db_host = $fuw_db_host" >> ansible.properties
echo "fuw_db_user = $fuw_db_user" >> ansible.properties
echo "fuw_db_password = $fuw_db_password" >> ansible.properties
echo "fuw_db_database = $fuw_db_database" >> ansible.properties

echo "deployment_target = $deployment_target" >> ansible.properties
echo "gitlab_project = $gitlab_project" >> ansible.properties
echo "ssh_private_key_file = $aws_ssh_key" >> ansible.properties
echo "gitlab_private_token= $GITLAB_PRIVATE_TOKEN" >> ansible.properties

# Required to upload database dump files to S3
aws_access_key_id=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/AWS_ACCESS_KEY_ID" | jq -r .value)
aws_secret_access_key=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/AWS_SECRET_ACCESS_KEY" | jq -r .value)

echo "aws_access_key_id = $aws_access_key_id" >> ansible.properties
echo "aws_secret_access_key = $aws_secret_access_key" >> ansible.properties

# Retrieve ips of provisioned ec2 instances
bastion_ip=$(terraform output ec2_bastion_public_ip | sed 's/"//g')
webapp_ip=$(terraform output ec2_private_ip | sed 's/"//g')

echo "ec2_bastion_login_account = centos@$bastion_ip" >> ansible.properties

# variables needed by disk-usage-monitor
gitter_room_id=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$FORK_VARIABLES_URL/GITTER_IT_NOTIFICATION_ROOM_ID" | jq -r .value)
gitter_api_token=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$FORK_VARIABLES_URL/GITTER_API_TOKEN" | jq -r .value)
echo "gitter_room_id = $gitter_room_id" >> ansible.properties
echo "gitter_api_token = $gitter_api_token" >> ansible.properties


# variables needed for monitoring provisioning
prometheus_aws_access_key=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$FORK_VARIABLES_URL/prometheus_aws_access_key" | jq -r .value)
prometheus_aws_private_key=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$FORK_VARIABLES_URL/prometheus_aws_private_key" | jq -r .value)
grafana_contact_smtp_host=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$FORK_VARIABLES_URL/grafana_contact_smtp_host" | jq -r .value)
grafana_contact_smtp_user=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$FORK_VARIABLES_URL/grafana_contact_smtp_user" | jq -r .value)
grafana_contact_smtp_password=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$FORK_VARIABLES_URL/grafana_contact_smtp_password" | jq -r .value)
grafana_contact_smtp_from_address=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$FORK_VARIABLES_URL/grafana_contact_smtp_from_address" | jq -r .value)
grafana_contact_smtp_from_name=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$FORK_VARIABLES_URL/grafana_contact_smtp_from_name" | jq -r .value)
echo "prometheus_aws_access_key = $prometheus_aws_access_key" >> ansible.properties
echo "prometheus_aws_private_key = $prometheus_aws_private_key" >> ansible.properties
echo "grafana_contact_smtp_host = $grafana_contact_smtp_host" >> ansible.properties
echo "grafana_contact_smtp_user = $grafana_contact_smtp_user" >> ansible.properties
echo "grafana_contact_smtp_password = $grafana_contact_smtp_password" >> ansible.properties
echo "grafana_contact_smtp_from_address = $grafana_contact_smtp_from_address" >> ansible.properties
echo "grafana_contact_smtp_from_name = $grafana_contact_smtp_from_name" >> ansible.properties

# Add newly created vms to known host file
# Remove old key
ssh-keygen -R $bastion_ip
ssh-keygen -R $webapp_ip
# Add the new key
ssh-keyscan -t ecdsa $bastion_ip >> ~/.ssh/known_hosts
new_host=$(ssh -i $aws_ssh_key centos@$bastion_ip ssh-keyscan -t ecdsa $webapp_ip)
echo $new_host  >> ~/.ssh/known_hosts
