# Provisioning production environments

The production infrastructure is made of 4 environments
* Staging for current production
* Live for current production
* Staging for Hot stand-by
* Live for Hot stand-by

Make sure you are familiar with the following documents beforehand:
* docs/sop/DEPLOYING_TO_PRODUCTION.md
* docs/SETUP_CI_CD_PIPELINE.md
* docs/SETUP_PROVISIONING.md


## Common preparation

As instructed in the "Configuring and using your local checkout to work with Upstream projects" section of `docs/sop/DEPLOYING_TO_PRODUCTION.md`, you should have to checkouts of the gigadb-website repo:
* `gigadb-upstream`,
* `gigadb-alt-upstream`

And you have performed the local setup described in "Configuring locally AWS for Upstream projects" section of `docs/sop/DEPLOYING_TO_PRODUCTION.md`

It is also expected that the Gitlab variables for both Upstream projects have been setup as generally described in `docs/SETUP_CI_CD_PIPELINE.md`, with the production specificities from the "Gitlab variables" section of `docs/sop/DEPLOYING_TO_PRODUCTION.md`.

Similarly, you should have performed the AWS dashboard actions from the "AWS dashboard" section of `docs/SETUP_PROVISIONING.md`

Please note each Upstream projects are deployed on different regions. The source of truth regarding which region and which role is associated with each project is the table at the top of `docs/sop/DEPLOYING_TO_PRODUCTION.md`.

But for simplicity, in the remainder of this document, we assume that `upstream/gigadb-website` project is in `ap-east-1`AWS region, and `upstream/alt-gigadb-website` project is in `ap-southeast-2` AWS Region.

Their respective local checkout name will be `gigadb-upstream` for the former project, and `gigadb-alt-upstream` for the latter project


## Staging for upstream/gigadb-website

Before you are able to create a `live` deployment, you must first deploy a  `staging` environment.

Change directory to the local checkout of the `upstream/gigadb-website` project:

```
cd gigadb-upstream
```

Change directory to the `envs` folder:
```  
cd ops/infrastructure/envs  
```  

Create directory `staging` directory if not existing already and change to it:
```  
mkdir staging
```
```  
cd staging  
```  

Initialise Terraform, including creating a new state (or retrieving if existing) on Gitlab and copying terraform files to `staging` environment:
```   
../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env staging  
 ```
It's an interactive command and it will ask question you will need to answer like:
  ```
	You need to specify the path to the ssh private key to use to connect to the EC2 instance: </path/to/your-ssh-key-for-hk-region>  
      
    You need to specify your GitLab username: <user input>  
      
    You need to specify a backup file created by the files-url-updater tool: </path/to/giagdbv3_*_v9.3.5.backup> (optional)  
      
    You need to specify an AWS region: ap-east-1  
 ```  

Alternatively you can specify most of the requested information as parameters to the commands:
```  
./../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env staging --region ap-east-1 --ssh-key /path/to/your-ssh-key-for-hk-region --web-ec2-type t3.medium --bastion-ec2-type t3.medium --rds-ec2-type "t3.large"  
```  

You can now  provision production staging server using the appropriate profile:
```  
AWS_PROFILE=Upstream terraform show   
```
>**Note**: will show all the existing resources, which should be the same as the terraform state file `staging_infra` from gitlab.

```
AWS_PROFILE=Upstream terraform plan
```
>**Note**:   terraform is idempotent and should not try to create new instances for already existing upstream staging, unless the new instance is expected to create.

```
AWS_PROFILE=Upstream terraform apply
```
>**Note**: will make changes to the existing infrastructure and update the terraform state file, input `yest` if the changes are expected to make.

```  
AWS_PROFILE=Upstream terraform refresh  
```  
>**Note**: will associate the public IP address from the EIPs to the terraform variable for public IPs


Next, Initialise Ansible including copying ansible files into `staging` environment and save to Gitlab variables the IP addresses output by terraform with this command:
```  
../../../scripts/ansible_init.sh --env staging  
```  

Install third party Ansible roles:
```  
ansible-galaxy install -r ../../../infrastructure/requirements.yml  
```  

Provision the bastion server:
```  
env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=staging"  
```  

Provision web server:
```  
env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=staging"
```  

Provision files server:
```  
env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories files_playbook.yml --extra-vars="gigadb_env=staging"  
```


At this stage you can trigger a pipeline for the `develop` branch on the `upstream/gigadb-website` Gitlab project:

Go to [Gitlab Upstream pipeline page](https://gitlab.com/gigascience/upstream/gigadb-website/-/pipelines)
and run all the jobs in the staging build stage in your pipeline, and press "Run pipeline",
then select`develop` and confirm   by clicking "Run pipeline".
This will execute all automated jobs.
There is a couple of manual jobs that will also need triggering: `PureFtpdBuildStaging` and then `PureFtpdDeployStaging`.


When the manual and automated jobs have all completed successfully, it will result in a partial deployment of GigaDB website to the infrastructure we've just provisioned.
Finally, you can perform the last step which is to load the environment's database server with data and install the tools on bastion servers needed by the users:

```
env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories data_cliapp_playbook.yml -e "gigadb_env=staging"
```

The website should be visitable at https://staging.gigadb.org, and the bastion server ready at bastion-stg.gigadb.host.

## Staging for upstream/alt-gigab-website

The provisioning for this project is similar to the other one, except that you need changing the key parameters that are
described in the table in the "Upstream projects" section at the top of `docs/sop/DEPLOYING_TO_PRODUCTION.md`

Change directory to the local checkout of the `upstream/alt-gigadb-website` project:

```
cd gigadb-alt-upstream
```

Change directory to the `envs` folder:
```  
cd ops/infrastructure/envs  
```  

Create directory `staging` directory if not existing already and change to it:
```  
mkdir staging
```
```  
cd staging  
```  

Initialise Terraform, including creating a new state (or retrieving if existing) on Gitlab and copying terraform files to `staging` environment:
```   
../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env staging  
 ```
It's an interactive command and it will ask question you will need to answer like:
  ```
	You need to specify the path to the ssh private key to use to connect to the EC2 instance: </path/to/your-ssh-key-for-sydney-region>  
      
    You need to specify your GitLab username: <user input>  
      
    You need to specify a backup file created by the files-url-updater tool: </path/to/giagdbv3_*_v9.3.5.backup> (optional)  
      
    You need to specify an AWS region: ap-southeast-2  
 ```  

Alternatively you can specify most of the requested information as parameters to the commands:
```  
./../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env staging --region ap-southeast-2 --ssh-key /path/to/your-ssh-key-for-sydney-region --web-ec2-type t3.medium --bastion-ec2-type t3.medium --rds-ec2-type "t3.large"  
```  

You can now  provision production staging server using the appropriate profile:
```  
AWS_PROFILE=UpstreamAlt terraform show   
```
>**Note**: will show all the existing resources, which should be the same as the terraform state file `staging_infra` from gitlab.

```
AWS_PROFILE=UpstreamAlt terraform plan
```
>**Note**:   terraform is idempotent and should not try to create new instances for already existing upstream staging, unless the new instance is expected to create.

```
AWS_PROFILE=UpstreamAlt terraform apply
```
>**Note**: will make changes to the existing infrastructure and update the terraform state file, input `yest` if the changes are expected to make.

```  
AWS_PROFILE=UpstreamAlt terraform refresh  
```  
>**Note**: will associate the public IP address from the EIPs to the terraform variable for public IPs


Next, Initialise Ansible including copying ansible files into `staging` environment and save to Gitlab variables the IP addresses output by terraform with this command:
```  
../../../scripts/ansible_init.sh --env staging  
```  

Install third party Ansible roles:
```  
ansible-galaxy install -r ../../../infrastructure/requirements.yml  
```  

Provision the bastion server:
```  
env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=staging"  
```  

Provision web server:
```  
env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=staging"
```  

Provision files server:
```  
env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories files_playbook.yml --extra-vars="gigadb_env=staging"  
```


At this stage you can trigger a pipeline for the `develop` branch on the `upstream/gigadb-website` Gitlab project:

Go to [Gitlab Upstream pipeline page](https://gitlab.com/gigascience/upstream/alt-gigadb-website/-/pipelines)
and run all the jobs in the staging build stage in your pipeline, and press "Run pipeline",
then select`develop` and confirm   by clicking "Run pipeline".
This will execute all automated jobs.
There is a couple of manual jobs that will also need triggering: `PureFtpdBuildStaging` and then `PureFtpdDeployStaging`.


When the manual and automated jobs have all completed successfully, it will result in a partial deployment of GigaDB website to the infrastructure we've just provisioned.
Finally, you can perform the last step which is to load the environment's database server with data and install the tools on bastion servers needed by the users:

```
env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories data_cliapp_playbook.yml -e "gigadb_env=staging"
```

The website should be visitable at https://alt-staging.gigadb.host, and the bastion server ready at bastion.alt-staging.gigadb.host

## Provisioning live production environments

In a blue/green infrastructure, when the infrastructure is alreay in place, any change through deployment of a new release
need to be performed first on the Hot stand-by infrastructure. So that the Current Production infrastructure is not impacted 
by potential fallouts from deployment of that particular change.

That's why the next couple of sections that cover provisioning of live production environment for each Upstream projects
won't cover the deployment from Gitlab part.
Instead, we have an additional section call "Deployment to a specific live environment" which will need to be used 
in conjunction with the "Deploying releases" section of `docs/sop/DEPLOYING_TO_PRODUCTION.md`
to figure out which protocol to apply that section with.

If the release includes changes to the infrastructure, then provisioning should be performed again, starting on the Hot Stand-by
infrastructure.

Which of the two projects is associated with Current Production and which one is associated with Hot Stand-by is tracked 
by the table in the "Upstream projects" section at the top of `docs/sop/DEPLOYING_TO_PRODUCTION.md`

### Provisioning live for upstream/gigadb-website

Change directory to the local checkout of the `upstream/gigadb-website` project:

```
cd gigadb-upstream
```

Change directory to the `envs` folder:
```  
cd ops/infrastructure/envs  
```  

Create directory `live` directory if not existing already and change to it:
```  
mkdir live
```
```  
cd live  
```  

Initialise Terraform, including creating a new state (or retrieving if existing) on Gitlab and copying terraform files to `live` environment:
```   
../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env live  
 ```
It's an interactive command and it will ask question you will need to answer like:
  ```
	You need to specify the path to the ssh private key to use to connect to the EC2 instance: </path/to/your-ssh-key-for-hk-region>  
      
    You need to specify your GitLab username: <user input>  
      
    You need to specify a backup file created by the files-url-updater tool: </path/to/giagdbv3_*_v9.3.5.backup> (optional)  
      
    You need to specify an AWS region: ap-east-1  
 ```  

Alternatively you can specify most of the requested information as parameters to the commands:
```  
./../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env live --region ap-east-1 --ssh-key /path/to/your-ssh-key-for-hk-region --web-ec2-type t3.medium --bastion-ec2-type t3.medium --rds-ec2-type "t3.large"  
```  

You can now  provision production staging server using the appropriate profile:
```  
AWS_PROFILE=Upstream terraform show   
```
>**Note**: will show all the existing resources, which should be the same as the terraform state file `live_infra` from gitlab.

```
AWS_PROFILE=Upstream terraform plan
```
>**Note**:   terraform is idempotent and should not try to create new instances for already existing upstream staging, unless the new instance is expected to create.

```
AWS_PROFILE=Upstream terraform apply
```
>**Note**: will make changes to the existing infrastructure and update the terraform state file, input `yest` if the changes are expected to make.

```  
AWS_PROFILE=Upstream terraform refresh  
```  
>**Note**: will associate the public IP address from the EIPs to the terraform variable for public IPs


Next, Initialise Ansible including copying ansible files into `live` environment and save to Gitlab variables the IP addresses output by terraform with this command:
```  
../../../scripts/ansible_init.sh --env live  
```  

Install third party Ansible roles:
```  
ansible-galaxy install -r ../../../infrastructure/requirements.yml  
```  

Provision the bastion server:
```  
env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=live"  
```  

Provision web server:
```  
env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=liveds
```  

Provision files server:
```  
env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories files_playbook.yml --extra-vars="gigadb_env=live"  
```



### Provisioning live for upstream/alt-gigadb-website

Change directory to the local checkout of the `upstream/alt-gigadb-website` project:

```
cd gigadb-alt-upstream
```

Change directory to the `envs` folder:
```  
cd ops/infrastructure/envs  
```  

Create directory `live` directory if not existing already and change to it:
```  
mkdir live
```
```  
cd live  
```  

Initialise Terraform, including creating a new state (or retrieving if existing) on Gitlab and copying terraform files to `live` environment:
```   
../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env live  
 ```
It's an interactive command and it will ask question you will need to answer like:
  ```
	You need to specify the path to the ssh private key to use to connect to the EC2 instance: </path/to/your-ssh-key-for-sydney-region>  
      
    You need to specify your GitLab username: <user input>  
      
    You need to specify a backup file created by the files-url-updater tool: </path/to/giagdbv3_*_v9.3.5.backup> (optional)  
      
    You need to specify an AWS region: ap-southeast-2  
 ```  

Alternatively you can specify most of the requrested information as parameters to the commands:
```  
./../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env live --region ap-southeast-2 --ssh-key /path/to/your-ssh-key-for-sydney-region --web-ec2-type t3.medium --bastion-ec2-type t3.medium --rds-ec2-type "t3.large"  
```  

You can now  provision production staging server using the appropriate profile:
```  
AWS_PROFILE=UpstreamAlt terraform show   
```
>**Note**: will show all the existing resources, which should be the same as the terraform state file `live_infra` from gitlab.

```
AWS_PROFILE=UpstreamAlt terraform plan
```
>**Note**:   terraform is idempotent and should not try to create new instances for already existing upstream staging, unless the new instance is expected to create.

```
AWS_PROFILE=UpstreamAlt terraform apply
```
>**Note**: will make changes to the existing infrastructure and update the terraform state file, input `yest` if the changes are expected to make.

```  
AWS_PROFILE=UpstreamAlt terraform refresh  
```  
>**Note**: will associate the public IP address from the EIPs to the terraform variable for public IPs


Next, Initialise Ansible including copying ansible files into `live` environment and save to Gitlab variables the IP addresses output by terraform with this command:
```  
../../../scripts/ansible_init.sh --env live  
```  

Install third party Ansible roles:
```  
ansible-galaxy install -r ../../../infrastructure/requirements.yml  
```  

Provision the bastion server:
```  
env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=live"  
```  

Provision web server:
```  
env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=live"
```  

Provision files server:
```  
env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories files_playbook.yml --extra-vars="gigadb_env=live"  
```

### Deployment to a specific live environment

>**Important**: Only preform this section after checking the "Blue/green deployment of the release" of `docs/sop/DEPLOYING_TO_PRODUCTION.md`

Trigger a pipeline for the release tag you want to deploy on the suitable Gitlab project:

Go to either [Gitlab Upstream pipeline page](https://gitlab.com/gigascience/upstream/gigadb-website/-/pipelines)
or [Gitlab UpstreamAlt pipeline page](https://gitlab.com/gigascience/upstream/alt-gigadb-website/-/pipelines)
depending on which on you are deploying to
and run all the jobs in the staging build stage in your pipeline, and press "Run pipeline",
then select`develop` and confirm   by clicking "Run pipeline".
This will execute all automated jobs.
There is a couple of manual jobs that will also need triggering: `PureFtpdBuildStaging` and then `PureFtpdDeployStaging`.

Since this is live production environment, you will need to build and deploy Tideways by running the manual jobs: `TidewaysBuildLive` and then `TidewaysDeployLive`

When the manual and automated jobs have all completed successfully, it will result in a partial deployment of GigaDB website to the infrastructure we've just provisioned.
Next, you can perform the last step which is to load the environment's database server with data and install the tools on bastion servers needed by the users:

```
env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories data_cliapp_playbook.yml -e "gigadb_env=staging"
```

Finally, you need to warm the cache by following instructions from the wiki:
https://github.com/gigascience/gigadb-website/wiki/How-to-warm-the-in%E2%80%90memory-cache

The website should be visitable at https://alt-live.gigadb.host, and the bastion server ready at bastion.alt-live.gigadb.host if you were deploying the Hot Stand-by.
The website should be visitable at https://gigadb.org, and the bastion server ready at bastion.gigadb.host if you were deploying the Current Production.


## Additional features for executing ansible playbooks:
```
# display all available plays
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=live" --list-tags
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=live" --list-tags
# execute selected plays
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=live" --tags files-url-updater,rclone-tool
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=live" --tags setup-docker-ce
# skip selected plays
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "backupDate=latest" --extra-vars="gigadb_env=live" --skip-tags fix-centos-eol-issues,setup-fail2ban,setup-docker-ce,restore-db-on-rds,load-latest-db
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=live" --skip-tags fix-centos-eol-issues,setup-fail2ban
```
