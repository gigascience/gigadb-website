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
cp ../../files_playbook.yml .
cp ../../bastion_playbook.yml .
cp ../../users_playbook.yml .
cp ../../monitoring_playbook.yml .
cp ../../bootstrap_playbook.yml .

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

# Required by rclone.conf.j2 for bastion server to copy readme files to Wasabi bucket
wasabi_access_key_id=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/WASABI_ACCESS_KEY_ID?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
wasabi_secret_access_key=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/WASABI_SECRET_ACCESS_KEY?filter%5benvironment_scope%5d=$target_environment" | jq -r .value)
echo "wasabi_access_key_id = $wasabi_access_key_id" >> ansible.properties
echo "wasabi_secret_access_key = $wasabi_secret_access_key" >> ansible.properties

echo "deployment_target = $deployment_target" >> ansible.properties
echo "gitlab_project = $gitlab_project" >> ansible.properties
echo "ssh_private_key_file = $aws_ssh_key" >> ansible.properties
echo "gitlab_private_token= $GITLAB_PRIVATE_TOKEN" >> ansible.properties

# Required to upload database dump files to S3
aws_access_key_id=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/AWS_ACCESS_KEY_ID" | jq -r .value)
aws_secret_access_key=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$PROJECT_VARIABLES_URL/AWS_SECRET_ACCESS_KEY" | jq -r .value)

echo "aws_access_key_id = $aws_access_key_id" >> ansible.properties
echo "aws_secret_access_key = $aws_secret_access_key" >> ansible.properties

# Required to upload md5 values and file sizes to S3 bucket - gigadb-datasets-metadata
gigadb_dataset_metadata_aws_access_key_id=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$MISC_VARIABLES_URL/gigadb_dataset_metadata_aws_access_key_id" | jq -r .value)
gigadb_datasets_metadata_aws_secret_access_key=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$MISC_VARIABLES_URL/gigadb_datasets_metadata_aws_secret_access_key" | jq -r .value)

echo "gigadb_dataset_metadata_aws_access_key_id = $gigadb_dataset_metadata_aws_access_key_id" >> ansible.properties
echo "gigadb_datasets_metadata_aws_secret_access_key = $gigadb_datasets_metadata_aws_secret_access_key" >> ansible.properties

# Required to upload dataset files to S3 bucket - gigadb-datasetfiles-backup
gigadb_datasetfiles_aws_access_key_id=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$MISC_VARIABLES_URL/gigadb_datasetfiles_aws_access_key_id" | jq -r .value)
gigadb_datasetfiles_aws_secret_access_key=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN" "$MISC_VARIABLES_URL/gigadb_datasetfiles_aws_secret_access_key" | jq -r .value)

echo "gigadb_datasetfiles_aws_access_key_id = $gigadb_datasetfiles_aws_access_key_id" >> ansible.properties
echo "gigadb_datasetfiles_aws_secret_access_key = $gigadb_datasetfiles_aws_secret_access_key" >> ansible.properties

# Retrieve ips of provisioned ec2 instances
bastion_private_ip=$(terraform output ec2_bastion_private_ip | sed 's/"//g')
bastion_ip=$(terraform output ec2_bastion_public_ip | sed 's/"//g')
webapp_private_ip=$(terraform output ec2_private_ip | sed 's/"//g')
webapp_ip=$(terraform output ec2_public_ip | sed 's/"//g')
files_private_ip=$(terraform output ec2_files_private_ip | sed 's/"//g')
files_ip=$(terraform output ec2_files_public_ip | sed 's/"//g')

echo "ec2_bastion_login_account = centos@$bastion_ip" >> ansible.properties

# Retrieve efs ids for mounting
efs_filesystem_dns_name=$(terraform output efs_filesystem_dns_name | sed 's/"//g')
configuration_area_id=$(terraform output efs_filesystem_configuration_area_id | sed 's/"//g')
dropbox_area_id=$(terraform output efs_filesystem_dropbox_area_id | sed 's/"//g')

{
  echo "efs_filesystem_dns_name = $efs_filesystem_dns_name"
  echo "configuration_area_id = $configuration_area_id"
  echo "dropbox_area_id = $dropbox_area_id"
} >> ansible.properties

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

echo  "\nRemove old key and add newly created vms to known host file"

ssh-keygen -R $bastion_ip
ssh-keygen -R $webapp_private_ip
ssh-keygen -R $files_private_ip
# Add the new key
ssh-keyscan -t ecdsa $bastion_ip >> ~/.ssh/known_hosts
web_host=$(ssh -i $aws_ssh_key centos"@$bastion_ip" ssh-keyscan -t ecdsa "$webapp_private_ip")
files_host=$(ssh -i $aws_ssh_key centos@"$bastion_ip" ssh-keyscan -t ecdsa "$files_private_ip")
echo "$web_host"  >> ~/.ssh/known_hosts
echo "$files_host"  >> ~/.ssh/known_hosts

# Bootstrap playbook
echo "Saving EC2 IP addresses to GitLab for web server"
env TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories bootstrap_playbook.yml --tags="webapp_ips" -e="private_ip=$webapp_private_ip public_ip=$webapp_ip" --extra-vars="gigadb_env=$target_environment"
echo "Saving EC2 IP addresses to GitLab for file server"
env TF_KEY_NAME=private_ip ansible-playbook -vvv -i ../../inventories bootstrap_playbook.yml --tags="files_ips" -e="private_ip=$files_private_ip public_ip=$files_ip" --extra-vars="gigadb_env=$target_environment"
echo "Saving EC2 IP addresses to GitLab for bastion server"
ansible-playbook -i ../../inventories bootstrap_playbook.yml --tags="bastion_ips" -e="private_ip=$bastion_private_ip public_ip=$bastion_ip" --extra-vars="gigadb_env=$target_environment"
