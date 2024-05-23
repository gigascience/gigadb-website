# Setup the developers AWS environments

## Prerequisite

* Have a Gitlab account that has been added to GigaScience project
* Have a project under "Gigascience/forks" named "\<github handle\>-gigadb-website
* Have the domain names registered and DNS A record pointing to the following EIPs (which also must be created):

| endpoint | environment | domain | EIP | 
| -- | -- | -- | -- | 
| web site (main) | staging | choose a domain  | eip-gigadb-staging-\<github handle\> |
| web site  (main)| live | choose a domain | eip-gigadb-live-\<github handle\> |
| web site (portainer) | staging | choose a domain  | eip-gigadb-staging-\<github handle\> |
| web site  (portainer)| live | choose a domain | eip-gigadb-live-\<github handle\> |
| bastion server | staging | choose a domain | eip-gigadb-bastion-staging-\<github handle\> |
| bastion server | live | choose a domain | eip-gigadb-bastion-live-\<github handle\> |
| files server | staging | choose a domain | eip-gigadb-files-staging-\<github handle\> |
| files server | live | choose a domain | eip-gigadb-files-live-\<github handle\> |



## Setting up your Staging environment

###  Provision AWS infrastructure

```
$ rm -rf ops/infrastructure/envs/staging
$ mkdir ops/infrastructure/envs/staging
$ cd ops/infrastructure/envs/staging/
$ ../../../scripts/tf_init.sh --project \<your gitlab project path\> --env staging --region \<your region\> --ssh-key \<path to your key\> --web-ec2-type t3.small --bastion-ec2-type t3.small
$ terraform plan
$ terraform apply
$ terraform refresh
```

### Configure the infastructure
 
```
$ ../../../scripts/ansible_init.sh --env staging
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories webapp_playbook.yml -e="gigadb_env=staging"
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories files_playbook.yml -e="gigadb_env=staging"
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "gigadb_env=staging" -e "backupDate=latest"
```

## Setting up your Live environment

###  Provision AWS infrastructure

```
$ rm -rf ops/infrastructure/envs/live
$ mkdir ops/infrastructure/envs/live
$ cd ops/infrastructure/envs/live/
$ ../../../scripts/tf_init.sh --project \<your gitlab project path\> --env live --region \<your region\> --ssh-key \<path to your key\> --web-ec2-type t3.small --bastion-ec2-type t3.small
$ terraform plan
$ terraform apply
$ terraform refresh
```

### Configure the infastructure
 
```
$ ../../../scripts/ansible_init.sh --env live
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories webapp_playbook.yml -e="gigadb_env=live"
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories files_playbook.yml -e="gigadb_env=live"
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "gigadb_env=live" -e "backupDate=latest"
```

## Tear down your environment

Don't forget to tear down your environment when you don't use it anymore:

```
$ cd ops/infrastructure/envs/staging
$ terraform destroy
```

or:

```
$ cd ops/infrastructure/envs/live
$ terraform destroy
```
