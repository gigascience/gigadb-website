# Provisioning of infrastructure on AWS

It is assumed you read through `docs/SETUP_CI_CD_PIPELINE.md` and preprate the Gitlab pipeline as documented.

## Relationship between Gitlab pipeline and provisioning

The continuous integration part of the Gitlab pipeline is not dependent on the AWS deployed infrastructure and can be run anytime.
The provisioning of a new AWS infrastructure and deployment of Gigadb application onto it requires 
orchestration between the Gitlab pipeline execution and the execution of the provisioning tools here, especially the Ansible playbook.

There are 5 phases of provisioning, and below they are listed alongside their relationship with Gitlab

| phase | description | operator actions | required Gitlab pipeline state |
| -- | -- | -- | -- |
| 1.TF init | The local terraform state is initialised from a remote Gitlab state (which is created on the fly if it doesn't exist) | runs `tf_init.sh` | no state required |
| 2.TF provisioning | The resources of the infrastructure are created on AWS | runs `terraform plan`, `terraform apply` and `terrafrom refresh` | no state required | 
| 3.Ansible init | The AWS resources details are uploaded to Gitlab variables, Ansible dependency are built, and playbook copied to the deployment environment directory | runs `ansible_init.sh`  |no state required |
| 4.Ansible provisioning | Configuration of the various GigaDB subsystems on each hosts of the infrastructure | executes the Ansible playbook | the build stage for the target environment should have run successfully | 
| 5.Application deployment | deployment of the application code from git repository | none on staging, manually trigger Gitlab deployment stage on live | the deployment stage of the target environment has been triggered and ran successfully |

>**Note:** You cannot run the build stage until you have run `ansible_init.sh`, because the build stage needs AWS resources details uploaded by that script

>**Note:** The above implies that you cannot run the Ansible playbooks until you have run the build stage of the Gitlab pipeline 

>**Note:** GigaDB application is made of web-based and backoffice subsystems. Their deployment requires the build stage of the Gitlab pipeline to have been performed first.
>Their deployment is performed using the deployment stage job (`sd_gigadb` or `ld_gigadb`) of the Gitlab pipeline for the web-based application 
>and using the Ansible playbook  `bastion_playbook.yml` for the backoffice subsystems.

## Provisioning How-to

Here's a quick run-down of what you need to do to provision and infrastructure and deploy the application. 
The remainders of sections explain in more details how to install the tools mentioned here and give additional info on the process.

### Pre-requisites: Setup pipeline and Install the tools

1. You have implemented the instructions in `docs/SETUP_CI_CD_PIPELINE.md`
2. Install AWS CLI by following AWS's instructions [here](https://docs.aws.amazon.com/cli/latest/userguide/cli-chap-install.html).
3. Install Terraform with `brew install terraform`
4. Install Ansible with `brew install ansible@9`

More details about the tools can be found in the "Tools" section futher down.

### Setting up your Staging environment

#### 1.Provision AWS infrastructure

```
$ rm -rf ops/infrastructure/envs/staging
$ mkdir ops/infrastructure/envs/staging
$ cd ops/infrastructure/envs/staging/
$ ../../../scripts/tf_init.sh --project \<your gitlab project path\> --env staging --region \<your region\> --ssh-key \<path to your key\> --web-ec2-type t3.small --bastion-ec2-type t3.small
$ AWS_PROFILE=gigadb terraform plan
$ AWS_PROFILE=gigadb terraform apply
$ AWS_PROFILE=gigadb terraform refresh
```

>**Note:** Core team members who are deploying on Upstream should use `AWS_PROFILE=upstream` or `AWS_PROFILE=upstreamAlt`

#### 2. Connect provisioning with Gitlab pipeline

```
$ ../../../scripts/ansible_init.sh --env staging
```
This will save the information Terraform retrieved from the just created AWS resources into Gitlab variables.
You can now start a Gitlab pipeline for the git branch or tag you want to deploy (the main branch is called `develop`).


#### 3. Configure the infastructure
 
```
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories webapp_playbook.yml -e="gigadb_env=staging"
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories files_playbook.yml -e="gigadb_env=staging"
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "gigadb_env=staging" -e "backupDate=latest"
```

#### 4. Deploy the web-based application and the FTP server

Use the Gitlab Pipelines dashboard to build the application (by triggering manual job ``build_staging``).
Once the production containers are build (in the Gitlab Containers Registry they are listed as production_\<service\>:\<environment\>),
We can proceed with deployment to the target environment by triggering manual job ``sd_gigadb`` (staging).

The application should be available at the url defined in $REMOTE_HOME_URL for the staging environment

For the FTP, trigger on the Gitlab pipeline the build job `PureFtpdBuildStaging`, and upon success trigger the deployment with `PureFtpdDeployStaging`.

>**Note:** The `build_staging` and `sd_gigadb` jobs are automated, so if you take too long to perform step 3, the jobs may run and fails.
>That's fine, you can just trigger the jobs manually whenever you have completed step 3.

#### 5. Configure the bastion server and deploy the back-office subsystems

Run the bastion playbook again to setup the rest of the backend infrastructure.
```
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "gigadb_env=staging" -e "backupDate=latest"
```

### Setting up your Live environment

After you have setup your staging environment, you can optionally set up a live environment.
In general this is not necessary, unless you want to test changes to infrastructure, Gitlab pipeline or provisioning scripts.

>**Note:** the live environment share the same Gitlab pipeline as the staging environment. 
>There are different stages on the pipeline, two (build and deploy) for staging, and two (build and deploy) for live.

>**Note:** only git tags can be deployed to the a live environmment, so if you know you want to test someting on the live environment,
> you need to pick up a git release tag when setting the Staging environment first.

####  1. Provision AWS infrastructure

```
$ rm -rf ops/infrastructure/envs/live
$ mkdir ops/infrastructure/envs/live
$ cd ops/infrastructure/envs/live/
$ ../../../scripts/tf_init.sh --project \<your gitlab project path\> --env live --region \<your region\> --ssh-key \<path to your key\> --web-ec2-type t3.small --bastion-ec2-type t3.small
$ AWS_PROFILE=gigadb terraform plan
$ AWS_PROFILE=gigadb terraform apply
$ AWS_PROFILE=gigadb terraform refresh
```

>**Note:** Core team members who are deploying on Upstream should use `AWS_PROFILE=upstream` or `AWS_PROFILE=upstreamAlt`

#### 2. Connect provisioning with Gitlab pipeline

```
$ ../../../scripts/ansible_init.sh --env live
```

This will save the information Terraform retrieved from the just created AWS resources into Gitlab variables.

#### 3. Configure the infastructure
 
```
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories webapp_playbook.yml -e="gigadb_env=live"
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories files_playbook.yml -e="gigadb_env=live"
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "gigadb_env=live" -e "backupDate=latest"
```

#### 4. Deploy the web-based application and the FTP server

Use the Gitlab Pipelines dashboard to build the application (by triggering manual job ``build_live``).
Once the production containers are build (in the Gitlab Containers Registry they are listed as production_\<service\>:\<environment\>),
We can proceed with deployment to the target environment by triggering manual job ``ld_gigadb`` (staging).

The application should be available at the url defined in $REMOTE_HOME_URL for the staging environment

For the FTP, trigger on the Gitlab pipeline the build job `PureFtpdBuildLive`, and upon success trigger the deployment with `PureFtpdDeployLive`.


#### 5. Configure the bastion server and deploy the back-office subsystems

```
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "gigadb_env=live" -e "backupDate=latest"
```

### Tear down your environment

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

## Tools

#### AWS-CLI

See [awsdocs/awscli.md](awsdocs/awscli.md)

#### Terraform

[Terraform](https://www.terraform.io) is a tool which allows you to describe and
instantiate infrastructure as code.

Install Terraform by downloading the installer from the 
[Terraform](https://www.terraform.io) web site or it can be installed using a 
package manager for your operating system. For example, macOS users can use 
[Macports](https://www.macports.org) or [HomeBrew](https://brew.sh/):
```
$ sudo port install terraform
```
or
```
$ brew install terraform
```

Terraform describes infrastructure as code in text files ending in *.tf* using Terraform's HCL language.
When the infrastructure code is organised in a modular way, it makes use of Terraform modules 
to keep distinct infrastructure components together in a clear and maintainable way.
An entry point to the set of modules is required and is called a **root-module**. 
In our project this file is `ops/infrastructure/terraform.tf` and the modules are located in 
``ops/infrastructure/modules/``

Each module has a `README.md` describing its purpose.

##### Terraform state

Terraform keep track of the state of the provisioned infrastructure.
That state represents the up-to-date snapshot of provisioned AWS resources and Terraform can also synchronise
that state with reality for when changes to the infrastructure happen outside of Terraform, using the ``terraform refresh`` command.
You can view the state in a meaningful visualiation using the ``terraform show`` command.
When provisioning with Terraform, your requests are translated into action onto the state file before Terraform replay them against the real infrastructure.
You have a chance to preview what those changes would be by executing the ``terraform plan`` command.
Actual changes to the infrastructure are performed with the command ``terraform apply``.

The state file Terraform works with can be a local file (ending with suffix .tfstate), but it is recommended 
to use a remote state backend which has the following benefits:

 * every team members who need to perform provisioning can start their work with a state that accurately represent reality 
 * a locking mechanism is available to prevent the same piece of infrastructure to be changed by several people at the same time
 * If a developer lose their development machine, they are not losing the map of what's provisioned

We use GitLab as our remote backend. When we want to operate Terraform, we initialise our remote state using the 
script ``ops/scripts/tf_init.sh``.

To avoid impactful mistakes, It's important to maintain a different state for each environment.
However there's no logical link between GitLab environment and Terraform state hosted in GitLab, so we need to make sure the 
name we choose for the Terraform state reflects the environment it is for.
``ops/scripts/tf_init.sh`` does that by accepting as parameter the target environment and prefix the state it initialise with 
that value.

Like GitLab pipelines and variables, Terraform state are specific to a GitLab project, so that's another input
that is passed as a parameter to the shell script.

Because the script needs to interact with GitLab API, the script needs to know the value for your ``GITLAB_USERNAME``
and your ``GITLAB_PRIVATE_TOKEN`` variable.
By default, it assumes they are defined in the ``.env`` file. If it cannot find them there,
it will prompt the user for value.
It also needs to know the path to the private ssh key that will allow Ansible to configure the target server, so the user 
will be prompted for that information.

``ops/scripts/tf_init.sh`` need to be used from the deployment environment directory, an environment specific directory, either ``ops/infrastructure/envs/staging``
or ``ops/infrastructure/envs/live``

This is how the script is run:

```
$ cd ops/infrastructure/envs/environment
$ ../../../scripts/tf_init.sh --project gigascience/forks/rija-gigadb-website --env environment --region region --ssh-key /Users/owner/.ssh/rm-gigadb-sshkey.pem --web-ec2-type t3.micro --bastion-ec2-type t3.micro
```

where we replace:
* ``gigascience/forks/rija-gigadb-website`` with the appropriate GitLab project.
* ``environment`` with ``staging`` or ``live``
* ``region`` with an AWS region where to deploy the infrastructure

You can type `../../../scripts/tf_init.sh --help` to get the list of all possible arguments.

The script also copies the ``ops/infrastructure/terraform.tf`` root module in the environment-specific directory so 
that terraform commands can be run from that directory.

##### Deployment environment directories

* For staging: ``cd ops/infractructure/envs/staging``
* For live: ``cd ops/infractructure/envs/live``

Those directories start empty, but the ``ops/scripts/tf_init.sh`` aforementioned and ``ops/scripts/ansible_init.sh`` below will populate them with necessary files
so we can run ``terraform`` and ``ansible-playbook`` commands from those directories for a safe provisioning of the desired environment.

In particular, the ``ops/scripts/tf_init.sh`` script will write in a ``.init_vars`` file the answer to the prompted value, so that they are not asked
again in subsequent runs. ``ops/scripts/ansible_init.sh`` will also source that file.

###### The list of files that will be created or placed in those directories during deployment:

| name | description | used by |
| --- | --- | --- |
| ansible.properties | created by ``ansible_init.sh``, holds variables assignment used by Ansible to configure deployed application| ansible-playbook | 
| getIAMUserNameToJSON.sh | copied by ``ansible_init.sh`` | terraform |
| output/ | created by ``ansible-playbook`` to store a copy of Docker certs | docker |
| webapp_playbook.yml | copied by ``ansible_init.sh`` | ansible-playbook | 
| files_playbook.yml | copied by ``ansible_init.sh`` | ansible-playbook | 
| users_playbook.yml | copied by ``ansible_init.sh`` | ansible-playbook | 
| bastion_playbook.yml | copied by ``ansible_init.sh`` | ansible-playbook | 
| bootstrap_playbook.yml | copied by ``ansible_init.sh`` | ansible-playbook | 
| terraform.tf | copied by ``ansible_init.sh`` | terraform |
| terraform.tfvars | created by ``ansible_init.sh`` to hold Terraform variables assigments | terraform | 

#### Ansible

[Ansible](https://www.ansible.com) is used to install the EC2 instance 
with a Docker daemon. In addition, a PostgreSQL database is created on the RDS
instance using `sql/production_like.pgdmp`.

You can install Ansible on macOS using [HomeBrew](https://brew.sh) with the command ``brew install ansible``

##### Ansible setup and configuration

Like Terraform, we run operate Ansible from the environment specific directories. The reasons are exactly the same as for Terraform (Safety, DRY, Cloud account flexibility).

The main concepts used in Ansible are Hosts, Roles, Tasks and Playbook.

The sets of software configuration instructions we want to perform on the infrastructure provisioned with Terraform in the previous step, are Tasks (e.g: Enable systemd service).

Tasks are grouped into Roles (e.g: docker-postinstall). 

The Playbook is the file describing the sequence of Roles (and/or Tasks) to be performed on a collection of Hosts whose software configuration we want to bring to a certain state.

Hosts can be defined statically, dynamically or a combination of both and from one or more sources.

Roles are used in Ansible to perform tasks on machines such as installing a  
software package. An Ansible role consists of a group of variables, tasks, files
and handlers stored in a standardised file structure. There are a number of
roles in `ops/infrastructure/roles` for installing Docker, PostgreSQL and
security tools on hosts. Other roles are required which are available from
public repositories.

Download these roles:
```
$ cd ops/infrastructure/envs/environment 
$ ansible-galaxy install -r ../../../infrastructure/requirements.yml
```
Where ``environment`` is replaced by ``staging`` or ``live``

Best practices is to use Ansible in agent-less way, so it needs connection parameters in order to control the remote provisioned machine.

The host name and IP address on which to run ansible are an output of running terraform, so we are going to feed ansible the host name and ip adress dynamically.

However, the connection parameters (like SSH keys) are variables we need to supply statically and they are different for each environment.

The machines controlled by Ansible are defined in a [`hosts`](https://github.com/gigascience/gigadb-website/blob/develop/ops/infrastructure/inventories/hosts)
file which lists the host machines connection details. Our file is located at `ops/infrastructure/inventories/hosts`.

>**Note:** host names in Ansible must be made of alphanumerical and underscore characters only. Although Terraform and AWS don't have that limitation, the Name tag needs to follow it so the connection between Terraform and Ansible can be made.


An ``ansible.properties`` file needs to exist in the environment-specific 
directory for a given environment. This file is queried by the host variables 
shown above, and is initially created with a `backup_file` variable when 
`$ ../../../scripts/tf_init.sh` was executed above. More variables are added 
into `ansible.properties` when `ops/scripts/ansible_init.sh` is executed:

```
$ cd ops/infrastructure/envs/environment
$ ../../../scripts/ansible_init.sh --env environment
```
Where environment must be replaced by ``staging`` or ``live``.

That script needs to be executed after ``ops/scripts/tf_init.sh`` has been run, as our script is dependent
on the existence of an ``.init_env_vars`` file created by the latter.

The `ansible_init.sh` script also makes a copy of:
* `ops/infrastructure/webapp_playbook.yml`
* `ops/infrastructure/files_playbook.yml`
* `ops/infrastructure/bastion_playbook.yml`
into the environment-specific 
directory so the playbooks can be performed from environment specific directory. 
This ansible script also updates the `gigadb_db_host` Gitlab variable with the 
domain name of the RDS service in preparation for its provisioning.

###### To enable provisioning web and files servers through a bastion server

Bastion server provides perimeter access control, it acts as an entry point into a network containing private network instances. 
Once dockerhost servers have been provisioned using terraform with ssh port restricted, which could then only be accessed the through bastion server. 

Adding `ansible_ssh_common_args` in `/inventories/hosts` will make ansible to do the provisioning on dockerhost servers through bastion host.

And prefixing the ansible commands with `TF_KEY_NAME=private_ip` to webapp_playbook.yml (or files_playbook.yml) is essential as it would force dockerhost server to only accept a private ip entry,
otherwise, `UNREACHEABLE !` would be occurred.

##### How to manually ssh to dockerhost through the bastion for debugging purpose
Sometimes, it would be useful to log into dockerhost server manually for debugging. There are two important points to keep in mind:
1. Get the public DNS or private ip address of web server or files server from `terraform output` or EC2 dashboard. 
2. All EC2 servers share the same ssh private key.  

Here are the steps:
```
# To check whether bastion server is accessible by logging in
user@dev-computer: % ssh -i ~/.ssh/<CustomPrivateKey>.pem centos@<bastion_public_ip>
[centos@<bastion_private_ip> ~]$ ls
database_bootstrap.backup
# Log in to dockerhost server through bastion by adding ProxyCommand to ssh command using public DNS
user@dev-computer: % ssh -i ~/.ssh/<CustomPrivateKey>.pem -o ProxyCommand="ssh -W %h:%p -i ~/.ssh/<CustomPrivateKey>.pem  centos@<bastion_public_ip>" centos@ec2-<docker_public_ip>.<region>.compute.amazonaws.com
[centos@<dockerhost_private_ip> ~]$ ls
app_data
# Log in to dockerhost server through bastion by adding ProxyCommand to ssh command using dockerhot private ip
user@dev-computer: % ssh -i ~/.ssh/<CustomPrivateKey>.pem -o ProxyCommand="ssh -W %h:%p -i ~/.ssh/<CustomPrivateKey>.pem  centos@<bastion_public_ip>" centos@<docker_private_ip>
[centos@<dockerhost_private_ip> ~]$ ls
app_data
```

###### Linking Terraform and Ansible.

Our `hosts` file does not list any machines. Instead, a tool called 
[`terraform-inventory`](https://github.com/adammck/terraform-inventory)  
generates a dynamic Ansible inventory from a Terraform state file. Nonetheless, 
the `hosts` file is still used to reference variables for hosts.

The terraform-inventory binary must be present on your dev machine. On mac, you can use Homebrew to install it

```
$ brew install terraform-inventory
```

Here is how to check the output of Terraform that are used as input to the Ansible workflow:
```
$ cd ops/infrastructure/envs/environment
$ ../../inventories/terraform-inventory.sh --list | jq -r
```
where ``environment`` is replaced by ``staging`` or ``live``, the environment for which Terraform has been previously run.


##### Ansible playbook execution

Provision the EC2 instance using Ansible:
```
$ cd ops/infrastructure/envs/staging
$ TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories webapp_playbook.yml
```

>Note that that **name_gigadb_server_staging_<IAMUser>** must match the "Name" tag associated to the AWS EC2 resource defined in ``ops/infrastructure/modules/aws-instance/aws-instance.tf`` for the environment of interest (here ``staging``):

```
  tags = {
    Name = "gigadb_server_${var.deployment_target}_${var.owner}",
    System = "t3_micro-centos8",
  }
```

>**Note:** host names in Ansible must be made of alphanumerical and underscore characters only. Although Terraform and AWS don't have that limitation, the Name tag needs to follow it so the connection between Terraform and Ansible can be made.


> Since an elastic IP address is being used, you might need to delete the entry
in the `~/.ssh/known_hosts` file associated with the elastic IP address if this
is not the first time you have performed this provisioning step. 

Ansible will update values for the project environment variables below in 
GitLab. Check them on the project environment variables page after the Ansible
provisioning has completed. This is done by the `docker-postinstall` role.

| Variable's Key | Environments | Description |
|---|---|---|
| docker_tlsauth_ca | staging | Certificate authority for staging server - this is provided by the staging server during Ansible provisioning |
| docker_tlsauth_cert | staging | Public certificate for staging server - this is provided by staging server during Ansible provisioning |
| docker_tlsauth_key  | staging | the server key for the above CA - this is provided by staging server during Ansible provisioning |
| docker_tlsauth_ca | live | Certificate authority for staging server - this is provided by the live server during Ansible provisioning |
| docker_tlsauth_cert | live | Public certificate for staging server - this is provided by live server during Ansible provisioning |
| docker_tlsauth_key  | live | the server key for the above CA - this is provided by live server during Ansible provisioning |
| remote_public_ip    | staging | Public IP address of staging server |
| remote_private_ip   | staging | Private IP address of staging server |
| remote_public_ip    | live | Public IP address of live server |
| remote_private_ip   | live | Private IP address of live server |

This is for running a secure Docker engine on the AWS EC2 server
so that the Docker API is secured over TCP and we know we are communicating 
with the correct server and not a malicious impersonation. We also need to 
authenticate the client with TLS so only clients using the client certificates 
can use the Docker engine. This is the 2-way certificate-based authentication.

>When Ansible generates the client/server certificate, it writes them on the EC2 instance at location ``/home/centos/.docker/``

>If an operator needs to perform a docker action on the EC2 instance from this development machine,
the three files constituting the client certificates `ca.pem`, `cert.pem` and `key.pem` in `ops/infrastructure/envs/<DEPLOY_ENV>/output/` or from gitlab variables need to be copied to ``~/.docker/`` in development machine.
Then the containers in dockerhost server can be accessed like this:
```
docker --tlsverify -H=<remote_public_ip>:2376 ps
```

The RDS instance is provisioned with a database via the bastion server by a
separate ansible playbook:
```
$ cd ops/infrastructure/envs/staging
$ ansible-playbook -i ../../inventories bastion_playbook.yml
```

The bastion playbook will create a `gigadb` database from RDS automated backups

### Further configuration steps

The new gigadb-website code contains functionality for running GigaDB over 
[HTTPS](https://en.wikipedia.org/wiki/HTTPS). 
[Let's Encrypt](https://letsencrypt.org) is used as a trusted certificate 
authority to sign a certificate provided by GigaDB which is trusted by users.

For Let's Encrypt to do this, the server used for staging GigaDB requires a 
domain name as mentioned above. The EC2 domain names provided by AWS cannot be 
used because they are ephemeral and so are blacklisted by Let's Encrypt. Your 
own domain name must be used instead, *e.g.* [http://gigadb-staging.gigatools.net] 
which Let's Encrypt will verify as a domain name under your control.

Currently, an EC2 instance is used to host a GigaDB staging server. This EC2 
instance has been allocated an elastic IP address which should be mapped onto 
the domain name (*e.g.* [http://gigadb-staging.gigatools.net]) using your domain 
name registry service. This domain name is provided with an SSL certificate by 
Let's Encrypt.

The deployment of the certificate is handled by the ``ops/scripts/setup_cert.sh`` script which 
is summoned during a deployment job (``sd_gigadb`` or ``ld_gigadb``).

#### NGINX configuration

In addition, NGINX conf files need to be configured with the domain name of the 
target server (staging or live).
The GitLab variable that needs to be set for this is ``$REMOTE_HOSTNAME``.

As part of executing ``ops/scripts/generate_config.sh`` by running ``docker-compose run --rm config``, the templates: 

* ops/configuration/nginx-conf/sites/nginx.target_deployment.http.conf.dist
* ops/configuration/nginx-conf/sites/nginx.target_deployment.https.conf.dist

will be used to create the final Nginx site configs file with the value of ``$REMOTE_HOSTNAME``

at the in-container location ``/etc/nginx/sites-available/``

The script ``ops/configuration/nginx-conf/enable_sites`` will enable the sites whose configuration are passed as parameter.

Example from ``ops/pipelines/gigadb-deploy-jobs.yml``
```
- docker-compose --tlsverify -H=$REMOTE_WEBAPP_DOCKER -f ops/deployment/docker-compose.production-envs.yml exec -T web /usr/local/bin/enable_sites gigadb.$GIGADB_ENV.https
```

When the container service ``web`` is started, it will also execute that ascript to enable the default http configuration for Gigadb.org,
so that a TLS certificates created with Let's Encrypt can pass verification.

The TLS certificate for the domain $REMOTE_HOSTNAME are managed by the script ``ops/scripts/setup_cert.sh``

It will create new certificates with Let's encrypt if none exists on the target deployment or remotely as GitLab variables.
if existing on the target deployment, the script will request renewal which Let's Encrypt will only perform if close to expiration.

Upon creation of new certificate or renewal, the certificates are backed up to GitLab as the following variables:

| name | environment | description |
| --- | --- | --- |
| tls_fullchain_pem | staging | all certificates, including server certificate (aka leaf certificate or end-entity certificate). The server certificate is the first one in this file, followed by any intermediates. |
| tls_privkey_pem | staging | private key for the certificate in PEM format |
| tls_chain_pem | staging | contains the additional intermediate certificate or certificates that web browsers will need in order to validate the server certificate |
| tls_fullchain_pem | live | all certificates, including server certificate (aka leaf certificate or end-entity certificate). The server certificate is the first one in this file, followed by any intermediates. |
| tls_privkey_pem | live | private key for the certificate in PEM format |
| tls_chain_pem | live | contains the additional intermediate certificate or certificates that web browsers will need in order to validate the server certificate |

When provisioning a new staging or live environment, ``ops/scripts/setup_cert.sh`` will attempt to pull these variables if they exist.
This way, we won't unnecessarily creat certificate everytime we need to re-create a staging and deployment, 
thus reducing the risk of hitting weekly rate-limit for certificate creation imposed by Let's Encrypt.

> Since an elastic IP address is being used, you might need to delete the entry
in the `~/.ssh/known_hosts` file associated with the elastic IP address if this
is not the first time you have performed the plays in the playbook.

### Restoration of database backups

```
#Go to environment directory
$ cd <path>/gigadb-website/ops/infrastructure/envs/staging

#Terminate existing RDS service
$ terraform destroy --target module.rds

#Copy override.tf to staging environment
$ ../../../scripts/tf_init.sh --project gigascience/forks/pli888-gigadb-website --env staging --restore-backup

#Backups can either be restored to its latest restorable time or to a specific
#time.

#To restore to latest restorable time - need to override database name as this 
#will come from the backup
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var use_latest_restorable_time="true"

#To restore to specific time in backup - need to override database name as this 
#will come from the backup
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var utc_restore_time="2021-10-27T06:02:12+00:00"
```

### Troubleshooting

If you meet the error:
 ```
 Error: Error launching source instance: MissingInput: No subnets found for the default VPC 'vpc-a717e2ce'. Please specify a subnet.
 ```

Then running the following command to create a default subnet will fix it:

 ```
 aws ec2 --profile UserNameOfAdminUser create-default-subnet --region ap-east-1 --availability-zone ap-east-1a
 ```

All SQL statements executed on the PostgreSQL RDS instance are logged in staging
deployments of GigaDB. These `postgres.log` files are available from the AWS
console for the RDS service in the `Log & events` tab.


### Tools

#### AWS-CLI

See [awsdocs/awscli.md](awsdocs/awscli.md)

#### Terraform

[Terraform](https://www.terraform.io) is a tool which allows you to describe and
instantiate infrastructure as code.

Install Terraform-0.14 by downloading the installer from the 
[Terraform](https://www.terraform.io) web site or it can be installed using a 
package manager for your operating system. For example, macOS users can use 
[Macports](https://www.macports.org) or [HomeBrew](https://brew.sh/):
```
$ sudo port install terraform
```
or
```
$ brew install terraform
```

Terraform describes infrastructure as code in text files ending in *.tf*.
When the infrastructure code is organised in a modular way, it makes use of Terraform modules 
to keep distinct infrastructure components together in a clear and maintainable way.
An entry point to the set of modules is required and is called a **root-module**. 
In our project this file is `ops/infrastructure/terraform.tf` and the modules are located in 
``ops/infrastructure/modules/``

Each module has a `README.md` that explains its purpose.

##### Terraform state

Terraform keep track of the state of the provisioned infrastructure.
That state represents the up-to-date snapshot of provisioned AWS resources and Terraform can also synchronise
that state with reality for when changes to the infrastructure happen outside of Terraform, using the ``terraform refresh`` command.
You can view the state in a meaningful visualiation using the ``terraform show`` command.
When provisioning with Terraform, your requests are translated into action onto the state file before Terraform replay them against the real infrastructure.
You have a chance to preview what those changes would be by executing the ``terraform plan`` command.
Actual changes to the infrastructure are performed with the command ``terraform apply``.

The state file Terraform works with can be a local file (ending with suffix .tfstate), but it is recommended 
to use a remote state backend wich has the following benefits:

 * every team members who need to perform provisioning can start their work with a state that accurately represent reality 
 * a locking mechanism is available to prevent the same piece of infrastructure to be changed by several people at the same time
 * If a developer lose their development machine, they are not losing the map of what's provisioned

We use GitLab as our remote backend. When we want to operate Terraform, we initialise our remote state using the 
script ``ops/scripts/tf_init.sh``.

To avoid impactful mistakes, It's important to maintain a different state for each environment.
However there's no logical link between GitLab environment and Terraform state hosted in GitLab, so we need to make sure the 
name we choose for the Terraform state reflects the environment it is for.
``ops/scripts/tf_init.sh`` does that by accepting as parameter the target environment and prefix the state it initialise with 
that value.

Like GitLab pipelines and variables, Terraform state are specific to a GitLab project, so that's another input
that is passed as a parameter to the shell script.

Because the script needs to interact with GitLab API, the script needs to know the value for your ``GITLAB_USERNAME``
and your ``GITLAB_PRIVATE_TOKEN`` variable.
By default, it assumes they are defined in the ``.env`` file. If it cannot find them there,
it will prompt the user for value.
It also needs to know the path to the private ssh key that will allow Ansible to configure the target server, so the user 
will be prompted for that information.

``ops/scripts/tf_init.sh`` need to be used from an environment specific directory, either ``ops/infrastructure/envs/staging``
or ``ops/infrastructure/envs/live``

This is how the script is run:

```
$ cd ops/infrastructure/envs/environment
$ ../../../scripts/tf_init.sh --project gigascience/forks/rija-gigadb-website --env environment --region region --ssh-key /Users/owner/.ssh/rm-gigadb-sshkey.pem --web-ec2-type t3.micro --bastion-ec2-type t3.micro
```

where we replace:
* ``gigascience/forks/rija-gigadb-website`` with the appropriate GitLab project.
* ``environment`` with ``staging`` or ``live``
* ``region`` with an AWS region where to deploy the infrastructure

You can type `../../../scripts/tf_init.sh --help` to get the list of all possible arguments.

The script also copies the ``ops/infrastructure/terraform.tf`` root module in the environment-specific directory so 
that terraform commands can be run from that directory.

##### Environment specific directories

* For staging: ``cd ops/infractructure/envs/staging``
* For live: ``cd ops/infractructure/envs/live``

Those directories start empty, but the ``ops/scripts/tf_init.sh`` aforementioned and ``ops/scripts/ansible_init.sh`` below will populate them with necessary files
so we can run ``terraform`` and ``ansible-playbook`` commands from those directories for a safe provisioning of the desired environment.

In particular, the ``ops/scripts/tf_init.sh`` script will write in a ``.init_vars`` file the answer to the prompted value, so that they are not asked
again in subsequent runs. ``ops/scripts/ansible_init.sh`` will also source that file.

###### The list of files that will be created or placed in those directories during deployment:

| name | description | used by |
| --- | --- | --- |
| ansible.properties | created by ``ansible_init.sh``, holds variables assignment used by Ansible to configure deployed application| ansible-playbook | 
| getIAMUserNameToJSON.sh | copied by ``ansible_init.sh`` | terraform |
| output/ | created by ``ansible-playbook`` to store a copy of Docker certs | docker |
| (webapp|files|users|bastion)_playbook.yml | copied by ``ansible_init.sh`` | ansible-playbook | 
| terraform.tf | copied by ``ansible_init.sh`` | terraform |
| terraform.tfvars | created by ``ansible_init.sh`` to hold Terraform variables assigments | terraform | 

#### Ansible

[Ansible](https://www.ansible.com) is used to install the EC2 instance 
with a Docker daemon. In addition, a PostgreSQL database is created on the RDS
instance using `sql/production_like.pgdmp`.

You can install Ansible on macOS using [HomeBrew](https://brew.sh) with the command ``brew install ansible@9``

>**Note:** Currently our setup support only Ansible core up to version 2.16.*. More recent versions will cause errors
> ansible-core 2.16.* comes with the [Ansible version 9.0.0](https://docs.ansible.com/ansible/latest/roadmap/COLLECTIONS_9.html) 
>(2.15.* comes with [Ansible version 8.0.0](https://docs.ansible.com/ansible/latest/roadmap/COLLECTIONS_8.html)), 
>Therefore do not install [Ansible version 10.0.0](https://docs.ansible.com/ansible/latest/roadmap/COLLECTIONS_10.html) for now. That's why the brew incantation specifies `@9`

>**Note:** You can also install Ansible using Python package with `pip3 install ansible=9.7.0`
>However if you choose that method you will have to adjust how you call the Ansible command appropriately
>as this documentation will assume you used Brew.

##### Ansible setup and configuration

Like Terraform, we run operate Ansible from the environment specific directories. The reasons are exactly the same as for Terraform (Safety, DRY, Cloud account flexibility).

The main concepts used in Ansible are Hosts, Roles, Tasks and Playbook.

The sets of software configuration instructions we want to perform on the infrastructure provisioned with Terraform in the previous step, are Tasks (e.g: Enable systemd service).

Tasks are grouped into Roles (e.g: docker-postinstall). 

The Playbook is the file describing the sequence of Roles (and/or Tasks) to be performed on a collection of Hosts whose software configuration we want to bring to a certain state.

Hosts can be defined statically, dynamically or a combination of both and from one or more sources.

Roles are used in Ansible to perform tasks on machines such as installing a  
software package. An Ansible role consists of a group of variables, tasks, files
and handlers stored in a standardised file structure. There are a number of
roles in `ops/infrastructure/roles` for installing Docker, PostgreSQL and
security tools on hosts. Other roles are required which are available from
public repositories.

Download these roles:
```
$ cd ops/infrastructure/envs/environment 
$ ansible-galaxy install -r ../../../infrastructure/requirements.yml
```
Where ``environment`` is replaced by ``staging`` or ``live``

Best practices is to use Ansible in agent-less way, so it needs connection parameters in order to control the remote provisioned machine.

The host name and IP address on which to run ansible are an output of running terraform, so we are going to feed ansible the host name and ip adress dynamically.

However, the connection parameters (like SSH keys) are variables we need to supply statically and they are different for each environment.

The machines controlled by Ansible are defined in a [`hosts`](https://github.com/gigascience/gigadb-website/blob/develop/ops/infrastructure/inventories/hosts)
file which lists the host machines connection details. Our file is located at `ops/infrastructure/inventories/hosts` 

>**Note:** host names in Ansible must be made of alphanumerical and underscore characters only. Although Terraform and AWS don't have that limitation, the Name tag needs to follow it so the connection between Terraform and Ansible can be made.

An ``ansible.properties`` file needs to exist in the environment-specific 
directory for a given environment. This file is queried by the host variables 
shown above, and is initially created with a `backup_file` variable when 
`$ ../../../scripts/tf_init.sh` was executed above. More variables are added 
into `ansible.properties` when `ops/scripts/ansible_init.sh` is executed:

```
$ cd ops/infrastructure/envs/environment
$ ../../../scripts/ansible_init.sh --env environment
```
Where environment must be replaced by ``staging`` or ``live``.

That script needs to be executed after ``ops/scripts/tf_init.sh`` has been run, as our script is dependent
on the existence of an ``.init_env_vars`` file created by the latter.

The `ansible_init.sh` script also makes a copy of:
* `ops/infrastructure/webapp_playbook.yml`
* `ops/infrastructure/files_playbook.yml`
* `ops/infrastructure/bastion_playbook.yml`
into the environment-specific 
directory so the playbooks can be performed from environment specific directory. 
This ansible script also updates the `gigadb_db_host` Gitlab variable with the 
domain name of the RDS service in preparation for its provisioning.

###### To enable provisioning web and files servers through a bastion server
Bastion server provides perimeter access control, it acts as an entry point into a network containing private network instances. 
Once dockerhost servers have been provisioned using terraform with ssh port restricted, which could then only be accessed the through bastion server. 

Adding `ansible_ssh_common_args` in `/inventories/hosts` will make ansible to do the provisioning on dockerhost servers through bastion host.

And prefixing the ansible commands with `TF_KEY_NAME=private_ip` to webapp_playbook.yml (or files_playbook.yml) is essential as it would force dockerhost server to only accept a private ip entry,
otherwise, `UNREACHEABLE !` would be occurred.

##### How to manually ssh to dockerhost through the bastion for debugging purpose
Sometimes, it would be useful to log into dockerhost server manually for debugging. There are two important points to keep in mind:
1. Get the public DNS or private ip address of web server or files server from `terraform output` or EC2 dashboard. 
2. All EC2 servers share the same ssh private key.  

Here are the steps:
```
# To check whether bastion server is accessible by logging in
user@dev-computer: % ssh -i ~/.ssh/<CustomPrivateKey>.pem centos@<bastion_public_ip>
[centos@<bastion_private_ip> ~]$ ls
database_bootstrap.backup
# Log in to dockerhost server through bastion by adding ProxyCommand to ssh command using public DNS
user@dev-computer: % ssh -i ~/.ssh/<CustomPrivateKey>.pem -o ProxyCommand="ssh -W %h:%p -i ~/.ssh/<CustomPrivateKey>.pem  centos@<bastion_public_ip>" centos@ec2-<docker_public_ip>.<region>.compute.amazonaws.com
[centos@<dockerhost_private_ip> ~]$ ls
app_data
# Log in to dockerhost server through bastion by adding ProxyCommand to ssh command using dockerhot private ip
user@dev-computer: % ssh -i ~/.ssh/<CustomPrivateKey>.pem -o ProxyCommand="ssh -W %h:%p -i ~/.ssh/<CustomPrivateKey>.pem  centos@<bastion_public_ip>" centos@<docker_private_ip>
[centos@<dockerhost_private_ip> ~]$ ls
app_data
```

###### Linking Terraform and Ansible.

Our `hosts` file does not list any machines. Instead, a tool called 
[`terraform-inventory`](https://github.com/adammck/terraform-inventory)  
generates a dynamic Ansible inventory from a Terraform state file. Nonetheless, 
the `hosts` file is still used to reference variables for hosts.

The terraform-inventory binary must be present on your dev machine. On mac, you can use Homebrew to install it

```
$ brew install terraform-inventory
```

Here is how to check the output of Terraform that are used as input to the Ansible workflow:
```
$ cd ops/infrastructure/envs/environment
$ ../../inventories/terraform-inventory.sh --list | jq -r
```
where ``environment`` is replaced by ``staging`` or ``live``, the environment for which Terraform has been previously run.


##### Ansible playbook execution

Provision the EC2 instance using Ansible:
```
$ cd ops/infrastructure/envs/staging
$ TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories webapp_playbook.yml
```

>Note that that **name_gigadb_server_staging_<IAMUser>** must match the "Name" tag associated to the AWS EC2 resource defined in ``ops/infrastructure/modules/aws-instance/aws-instance.tf`` for the environment of interest (here ``staging``):

```
  tags = {
    Name = "gigadb_server_${var.deployment_target}_${var.owner}",
    System = "t3_micro-centos8",
  }
```

>**Note:** host names in Ansible must be made of alphanumerical and underscore characters only. Although Terraform and AWS don't have that limitation, the Name tag needs to follow it so the connection between Terraform and Ansible can be made.


> Since an elastic IP address is being used, you might need to delete the entry
in the `~/.ssh/known_hosts` file associated with the elastic IP address if this
is not the first time you have performed this provisioning step. 

Ansible will update values for the project environment variables below in 
GitLab. Check them on the project environment variables page after the Ansible
provisioning has completed. This is done by the `docker-postinstall` role.

| Variable's Key | Environnments | Description |
|---|---|---|
| docker_tlsauth_ca | staging | Certificate authority for staging server - this is provided by the staging server during Ansible provisioning |
| docker_tlsauth_cert | staging | Public certificate for staging server - this is provided by staging server during Ansible provisioning |
| docker_tlsauth_key  | staging | the server key for the above CA - this is provided by staging server during Ansible provisioning |
| docker_tlsauth_ca | live | Certificate authority for staging server - this is provided by the live server during Ansible provisioning |
| docker_tlsauth_cert | live | Public certificate for staging server - this is provided by live server during Ansible provisioning |
| docker_tlsauth_key  | live | the server key for the above CA - this is provided by live server during Ansible provisioning |
| remote_public_ip    | staging | Public IP address of staging server |
| remote_private_ip   | staging | Private IP address of staging server |
| remote_public_ip    | live | Public IP address of live server |
| remote_private_ip   | live | Private IP address of live server |

This is for running a secure Docker engine on the AWS EC2 server
so that the Docker API is secured over TCP and we know we are communicating 
with the correct server and not a malicious impersonation. We also need to 
authenticate the client with TLS so only clients using the client certificates 
can use the Docker engine. This is the 2-way certificate-based authentication.

>When Ansible generates the client/server certificate, it writes them on the EC2 instance at location ``/home/centos/.docker/``

>If an operator needs to perform a docker action on the EC2 instance from this development machine,
the three files constituting the client certificates `ca.pem`, `cert.pem` and `key.pem` in `ops/infrastructure/envs/<DEPLOY_ENV>/output/` or from gitlab variables need to be copied to ``~/.docker/`` in development machine.
Then the containers in dockerhost server can be accessed like this:
```
docker --tlsverify -H=<remote_public_ip>:2376 ps
```

The RDS instance is provisioned with a database via the bastion server by a
separate ansible playbook:
```
$ cd ops/infrastructure/envs/staging
$ ansible-playbook -i ../../inventories bastion_playbook.yml
```

The bastion playbook will create a `gigadb` database containing data from
`sql/production_like.pgdmp`. This file is created by running `./up.sh` when
spinning up a local GigaDB on your development platform.

### Further configuration steps

The new gigadb-website code contains functionality for running GigaDB over 
[HTTPS](https://en.wikipedia.org/wiki/HTTPS). 
[Let's Encrypt](https://letsencrypt.org) is used as a trusted certificate 
authority to sign a certificate provided by GigaDB which is trusted by users.

For Let's Encrypt to do this, the server used for staging GigaDB requires a 
domain name as mentioned above. The EC2 domain names provided by AWS cannot be 
used because they are ephemeral and so are blacklisted by Let's Encrypt. Your 
own domain name must be used instead, *e.g.* [http://gigadb-staging.gigatools.net] 
which Let's Encrypt will verify as a domain name under your control.

Currently, an EC2 instance is used to host a GigaDB staging server. This EC2 
instance has been allocated an elastic IP address which should be mapped onto 
the domain name (*e.g.* [http://gigadb-staging.gigatools.net]) using your domain 
name registry service. This domain name is provided with an SSL certificate by 
Let's Encrypt.

The deployment of the certificate is handled by the ``ops/scripts/setup_cert.sh`` script which 
is summoned during a deployment job (``sd_gigadb`` or ``ld_gigadb``).

#### NGINX configuration

In addition, NGINX conf files need to be configured with the domain name of the 
target server (staging or live).
The GitLab variable that needs to be set for this is ``$REMOTE_HOSTNAME``.

As part of executing ``ops/scripts/generate_config.sh`` by running ``docker-compose run --rm config``, the templates: 

* ops/configuration/nginx-conf/sites/nginx.target_deployment.http.conf.dist
* ops/configuration/nginx-conf/sites/nginx.target_deployment.https.conf.dist

will be used to create the final Nginx site configs file with the value of ``$REMOTE_HOSTNAME``

at the in-container location ``/etc/nginx/sites-available/``

The script ``ops/configuration/nginx-conf/enable_sites`` will enable the sites whose configuration are passed as parameter.

Example from ``ops/pipelines/gigadb-deploy-jobs.yml``
```
- docker-compose --tlsverify -H=$REMOTE_WEBAPP_DOCKER -f ops/deployment/docker-compose.production-envs.yml exec -T web /usr/local/bin/enable_sites gigadb.$GIGADB_ENV.https
```

When the container service ``web`` is started, it will also execute that ascript to enable the default http configuration for Gigadb.org,
so that a TLS certificates created with Let's Encrypt can pass verification.

The TLS certificate for the domain $REMOTE_HOSTNAME are managed by the script ``ops/scripts/setup_cert.sh``

It will create new certificates with Let's encrypt if none exists on the target deployment or remotely as GitLab variables.
if existing on the target deployment, the script will request renewal which Let's Encrypt will only perform if close to expiration.

Upon creation of new certificate or renewal, the certificates are backed up to GitLab as the following variables:

| name | environment | description |
| --- | --- | --- |
| tls_fullchain_pem | staging | all certificates, including server certificate (aka leaf certificate or end-entity certificate). The server certificate is the first one in this file, followed by any intermediates. |
| tls_privkey_pem | staging | private key for the certificate in PEM format |
| tls_chain_pem | staging | contains the additional intermediate certificate or certificates that web browsers will need in order to validate the server certificate |
| tls_fullchain_pem | live | all certificates, including server certificate (aka leaf certificate or end-entity certificate). The server certificate is the first one in this file, followed by any intermediates. |
| tls_privkey_pem | live | private key for the certificate in PEM format |
| tls_chain_pem | live | contains the additional intermediate certificate or certificates that web browsers will need in order to validate the server certificate |

When provisioning a new staging or live environment, ``ops/scripts/setup_cert.sh`` will attempt to pull these variables if they exist.
This way, we won't unnecessarily creat certificate everytime we need to re-create a staging and deployment, 
thus reducing the risk of hitting weekly rate-limit for certificate creation imposed by Let's Encrypt.

### Restoration of database snapshots


```
#Go to environment directory
$ cd <path>/gigadb-website/ops/infrastructure/envs/staging
```

# Terminate existing RDS service

```
$ terraform destroy --target module.rds

#Restore database snapshot
$ terraform plan -var snapshot_identifier="snapshot-for-testing"
$ terraform apply -var snapshot_identifier="snapshot-for-testing"
$ terraform refresh
```

### Restoration of database backups

```
# Go to environment directory
$ cd <path>/gigadb-website/ops/infrastructure/envs/staging

# Terminate existing RDS service
$ terraform destroy --target module.rds

# Copy override.tf to staging environment
$ ../../../scripts/tf_init.sh --project gigascience/forks/pli888-gigadb-website --env staging --restore-backup

# Backups can either be restored to its latest restorable time or to a specific
# time.

# To restore to latest restorable time - need to override database name as this 
# will come from the backup
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var use_latest_restorable_time="true"

# To restore to specific time in backup - need to override database name as this 
# will come from the backup
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var utc_restore_time="2021-10-27T06:02:12+00:00"
```

### Troubleshooting

If you meet the error:
 ```
 Error: Error launching source instance: MissingInput: No subnets found for the default VPC 'vpc-a717e2ce'. Please specify a subnet.
 ```

Then running the following command to create a default subnet will fix it:

 ```
 aws ec2 --profile UserNameOfAdminUser create-default-subnet --region ap-east-1 --availability-zone ap-east-1a
 ```

All SQL statements executed on the PostgreSQL RDS instance are logged in staging
deployments of GigaDB. These `postgres.log` files are available from the AWS
console for the RDS service in the `Log & events` tab.###  Provision AWS infrastructure

```
$ rm -rf ops/infrastructure/envs/staging
$ mkdir ops/infrastructure/envs/staging
$ cd ops/infrastructure/envs/staging/
$ ../../../scripts/tf_init.sh --project \<your gitlab project path\> --env staging --region \<your region\> --ssh-key \<path to your key\> --web-ec2-type t3.small --bastion-ec2-type t3.small
$ AWS_PROFILE=gigadb terraform plan
$ AWS_PROFILE=gigadb terraform apply
$ AWS_PROFILE=gigadb terraform refresh
```

### Configure the infrastructure
 
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
$ AWS_PROFILE=gigadb terraform plan
$ AWS_PROFILE=gigadb terraform apply
$ AWS_PROFILE=gigadb terraform refresh
```

### Configure the infrastructure
 
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
$ AWS_PROFILE=gigadb terraform destroy
```

or:

```
$ cd ops/infrastructure/envs/live
$ AWS_PROFILE=gigadb terraform destroy
```
## Tools

### AWS-CLI

See [awsdocs/awscli.md](awsdocs/awscli.md)

### Terraform

[Terraform](https://www.terraform.io) is a tool which allows you to describe and
instantiate infrastructure as code.

Install Terraform by downloading the installer from the 
[Terraform](https://www.terraform.io) web site or it can be installed using a 
package manager for your operating system. For example, macOS users can use 
[Macports](https://www.macports.org) or [HomeBrew](https://brew.sh/):
```
$ sudo port install terraform
```
or
```
$ brew install terraform
```

Terraform describes infrastructure as code in text files ending in *.tf*.
When the infrastructure code is organised in a modular way, it makes use of Terraform modules 
to keep distinct infrastructure components together in a clear and maintainable way.
An entry point to the set of modules is required and is called a **root-module**. 
In our project this file is `ops/infrastructure/terraform.tf` and the modules are located in 
``ops/infrastructure/modules/``

Inside each module there is a README.md that eplains what the module is for.

##### Terraform state

Terraform keep track of the state of the provisioned infrastructure.
That state represents the up-to-date snapshot of provisioned AWS resources and Terraform can also synchronise
that state with reality for when changes to the infrastructure happen outside of Terraform, using the ``terraform refresh`` command.
You can view the state in a meaningful visualiation using the ``terraform show`` command.
When provisioning with Terraform, your requests are translated into action onto the state file before Terraform replay them against the real infrastructure.
You have a chance to preview what those changes would be by executing the ``terraform plan`` command.
Actual changes to the infrastructure are performed with the command ``terraform apply``.

The state file Terraform works with can be a local file (ending with suffix .tfstate), but it is recommended 
to use a remote state backend wich has the following benefits:

 * every team members who need to perform provisioning can start their work with a state that accurately represent reality 
 * a locking mechanism is available to prevent the same piece of infrastructure to be changed by several people at the same time
 * If a developer lose their development machine, they are not losing the map of what's provisioned

We use GitLab as our remote backend. When we want to operate Terraform, we initialise our remote state using the 
script ``ops/scripts/tf_init.sh``.

To avoid impactful mistakes, It's important to maintain a different state for each environment.
However there's no logical link between GitLab environment and Terraform state hosted in GitLab, so we need to make sure the 
name we choose for the Terraform state reflects the environment it is for.
``ops/scripts/tf_init.sh`` does that by accepting as parameter the target environment and prefix the state it initialise with 
that value.

Like GitLab pipelines and variables, Terraform state are specific to a GitLab project, so that's another input
that is passed as a parameter to the shell script.

Because the script needs to interact with GitLab API, the script needs to know the value for your ``GITLAB_USERNAME``
and your ``GITLAB_PRIVATE_TOKEN`` variable.
By default, it assumes they are defined in the ``.env`` file. If it cannot find them there,
it will prompt the user for value.
It also needs to know the path to the private ssh key that will allow Ansible to configure the target server, so the user 
will be prompted for that information.

``ops/scripts/tf_init.sh`` need to be used from an environment specific directory, either ``ops/infrastructure/envs/staging``
or ``ops/infrastructure/envs/live``

This is how the script is run:

```
$ cd ops/infrastructure/envs/environment
$ ../../../scripts/tf_init.sh --project gigascience/forks/rija-gigadb-website --env environment --region region --ssh-key /Users/owner/.ssh/rm-gigadb-sshkey.pem --web-ec2-type t3.micro --bastion-ec2-type t3.micro
```

where we replace:
* ``gigascience/forks/rija-gigadb-website`` with the appropriate GitLab project.
* ``environment`` with ``staging`` or ``live``
* ``region`` with an AWS region where to deploy the infrastructure

You can type `../../../scripts/tf_init.sh --help` to get the list of all possible arguments.

The script also copies the ``ops/infrastructure/terraform.tf`` root module in the environment-specific directory so 
that terraform commands can be run from that directory.

#### Environment specific directories

* For staging: ``cd ops/infractructure/envs/staging``
* For live: ``cd ops/infractructure/envs/live``

Those directories start empty, but the ``ops/scripts/tf_init.sh`` aforementioned and ``ops/scripts/ansible_init.sh`` below will populate them with necessary files
so we can run ``terraform`` and ``ansible-playbook`` commands from those directories for a safe provisioning of the desired environment.

In particular, the ``ops/scripts/tf_init.sh`` script will write in a ``.init_vars`` file the answer to the prompted value, so that they are not asked
again in subsequent runs. ``ops/scripts/ansible_init.sh`` will also source that file.

##### The list of files that will be created or placed in those directories during deployment:

| name | description | used by |
| --- | --- | --- |
| ansible.properties | created by ``ansible_init.sh``, holds variables assignment used by Ansible to configure deployed application| ansible-playbook | 
| getIAMUserNameToJSON.sh | copied by ``ansible_init.sh`` | terraform |
| output/ | created by ``ansible-playbook`` to store a copy of Docker certs | docker |
| (webapp|files|users|bastion)_playbook.yml | copied by ``ansible_init.sh`` | ansible-playbook | 
| terraform.tf | copied by ``ansible_init.sh`` | terraform |
| terraform.tfvars | created by ``ansible_init.sh`` to hold Terraform variables assigments | terraform | 

### Ansible

[Ansible](https://www.ansible.com) is used to install the EC2 instance 
with a Docker daemon. In addition, a PostgreSQL database is created on the RDS
instance using `sql/production_like.pgdmp`.

You can install Ansible on macOS using [HomeBrew](https://brew.sh) with the command ``brew install ansible``

##### Ansible setup and configuration

Like Terraform, we run operate Ansible from the environment specific directories. The reasons are exactly the same as for Terraform (Safety, DRY, Cloud account flexibility).

The main concepts used in Ansible are Hosts, Roles, Tasks and Playbook.

The sets of software configuration instructions we want to perform on the infrastructure provisioned with Terraform in the previous step, are Tasks (e.g: Enable systemd service).

Tasks are grouped into Roles (e.g: docker-postinstall). 

The Playbook is the file describing the sequence of Roles (and/or Tasks) to be performed on a collection of Hosts whose software configuration we want to bring to a certain state.

Hosts can be defined statically, dynamically or a combination of both and from one or more sources.

Roles are used in Ansible to perform tasks on machines such as installing a  
software package. An Ansible role consists of a group of variables, tasks, files
and handlers stored in a standardised file structure. There are a number of
roles in `ops/infrastructure/roles` for installing Docker, PostgreSQL and
security tools on hosts. Other roles are required which are available from
public repositories.

Download these roles:
```
$ cd ops/infrastructure/envs/environment 
$ ansible-galaxy install -r ../../../infrastructure/requirements.yml
```
Where ``environment`` is replaced by ``staging`` or ``live``

Best practices is to use Ansible in agent-less way, so it needs connection parameters in order to control the remote provisioned machine.

The host name and IP address on which to run ansible are an output of running terraform, so we are going to feed ansible the host name and ip adress dynamically.

However, the connection parameters (like SSH keys) are variables we need to supply statically and they are different for each environment.

The machines controlled by Ansible are defined in a [`hosts`](https://github.com/gigascience/gigadb-website/blob/develop/ops/infrastructure/inventories/hosts)
file which lists the host machines connection details. Our file is located at `ops/infrastructure/inventories/hosts` and here is the current content annotated:
```
[all:vars] # host variables needed by Ansible to configure software and services. Most have their value pulled from ansible.properties created with the ansible_init.sh script

gitlab_url = "https://gitlab.com/api/v4/projects/{{ lookup('ini', 'gitlab_project type=properties file=ansible.properties') | urlencode | regex_replace('/','%2F') }}"
ansible_ssh_private_key_file = "{{ lookup('ini', 'ssh_private_key_file type=properties file=ansible.properties') }}"
ansible_ssh_common_args="-o ProxyCommand='ssh -W %h:%p -q {{ lookup('ini', 'ec2_bastion_login_account type=properties file=ansible.properties') }} -i {{ lookup('ini', 'ssh_private_key_file type=properties file=ansible.properties') }}'"
ansible_user = "centos"
ansible_become = "true"
database_bootstrap = "../../../../sql/production_like.pgdmp"
gitlab_private_token = "{{ lookup('ini', 'gitlab_private_token type=properties file=ansible.properties') }}"
gigadb_environment = "{{ lookup('ini', 'deployment_target type=properties file=ansible.properties') }}"

pg_user = "{{ lookup('ini', 'gigadb_db_user type=properties file=ansible.properties') }}"
pg_password = "{{ lookup('ini', 'gigadb_db_password type=properties file=ansible.properties') }}"
pg_database = "{{ lookup('ini', 'gigadb_db_database type=properties file=ansible.properties') }}"
fuw_db_user = "{{ lookup('ini', 'fuw_db_user type=properties file=ansible.properties') }}"
fuw_db_password = "{{ lookup('ini', 'fuw_db_password type=properties file=ansible.properties') }}"
fuw_db_database = "{{ lookup('ini', 'fuw_db_database type=properties file=ansible.properties') }}"

backup_file = "{{ lookup('ini', 'backup_file type=properties file=ansible.properties') }}"
```

>**Note:** host names in Ansible must be made of alphanumerical and underscore characters only. Although Terraform and AWS don't have that limitation, the Name tag needs to follow it so the connection between Terraform and Ansible can be made.

An ``ansible.properties`` file needs to exist in the environment-specific 
directory for a given environment. This file is queried by the host variables 
shown above, and is initially created with a `backup_file` variable when 
`$ ../../../scripts/tf_init.sh` was executed above. More variables are added 
into `ansible.properties` when `ops/scripts/ansible_init.sh` is executed:

```
$ cd ops/infrastructure/envs/environment
$ ../../../scripts/ansible_init.sh --env environment
```
Where environment must be replaced by ``staging`` or ``live``.

That script needs to be executed after ``ops/scripts/tf_init.sh`` has been run, as our script is dependent
on the existence of an ``.init_env_vars`` file created by the latter.

The `ansible_init.sh` script also makes a copy of:
* `ops/infrastructure/webapp_playbook.yml`
* `ops/infrastructure/files_playbook.yml`
* `ops/infrastructure/bastion_playbook.yml`
into the environment-specific 
directory so the playbooks can be performed from environment specific directory. 
This ansible script also updates the `gigadb_db_host` Gitlab variable with the 
domain name of the RDS service in preparation for its provisioning.

###### To enable provisioning web and files servers through a bastion server
Bastion server provides perimeter access control, it acts as an entry point into a network containing private network instances. 
Once dockerhost servers have been provisioned using terraform with ssh port restricted, which could then only be accessed the through bastion server. 

Adding `ansible_ssh_common_args` in `/inventories/hosts` will make ansible to do the provisioning on dockerhost servers through bastion host.

And prefixing the ansible commands with `TF_KEY_NAME=private_ip` to webapp_playbook.yml (or files_playbook.yml) is essential as it would force dockerhost server to only accept a private ip entry,
otherwise, `UNREACHEABLE !` would be occurred.

##### How to manually ssh to dockerhost through the bastion for debugging purpose
Sometimes, it would be useful to log into dockerhost server manually for debugging. There are two important points to keep in mind:
1. Get the public DNS or private ip address of web server or files server from `terraform output` or EC2 dashboard. 
2. All EC2 servers share the same ssh private key.  

Here are the steps:
```
# To check whether bastion server is accessible by logging in
user@dev-computer: % ssh -i ~/.ssh/<CustomPrivateKey>.pem centos@<bastion_public_ip>
[centos@<bastion_private_ip> ~]$ ls
database_bootstrap.backup
# Log in to dockerhost server through bastion by adding ProxyCommand to ssh command using public DNS
user@dev-computer: % ssh -i ~/.ssh/<CustomPrivateKey>.pem -o ProxyCommand="ssh -W %h:%p -i ~/.ssh/<CustomPrivateKey>.pem  centos@<bastion_public_ip>" centos@ec2-<docker_public_ip>.<region>.compute.amazonaws.com
[centos@<dockerhost_private_ip> ~]$ ls
app_data
# Log in to dockerhost server through bastion by adding ProxyCommand to ssh command using dockerhot private ip
user@dev-computer: % ssh -i ~/.ssh/<CustomPrivateKey>.pem -o ProxyCommand="ssh -W %h:%p -i ~/.ssh/<CustomPrivateKey>.pem  centos@<bastion_public_ip>" centos@<docker_private_ip>
[centos@<dockerhost_private_ip> ~]$ ls
app_data
```

### Linking Terraform and Ansible.

Our `hosts` file does not list any machines. Instead, a tool called 
[`terraform-inventory`](https://github.com/adammck/terraform-inventory)  
generates a dynamic Ansible inventory from a Terraform state file. Nonetheless, 
the `hosts` file is still used to reference variables for hosts.

The terraform-inventory binary must be present on your dev machine. On mac, you can use Homebrew to install it

```
$ brew install terraform-inventory
```

Here is how to check the output of Terraform that are used as input to the Ansible workflow:
```
$ cd ops/infrastructure/envs/environment
$ ../../inventories/terraform-inventory.sh --list | jq -r
```
where ``environment`` is replaced by ``staging`` or ``live``, the environment for which Terraform has been previously run.


##### Ansible playbook execution

Provision the EC2 instance using Ansible:
```
$ cd ops/infrastructure/envs/staging
$ TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories webapp_playbook.yml
```

>Note that that **name_gigadb_server_staging_<IAMUser>** must match the "Name" tag associated to the AWS EC2 resource defined in ``ops/infrastructure/modules/aws-instance/aws-instance.tf`` for the environment of interest (here ``staging``):

```
  tags = {
    Name = "gigadb_server_${var.deployment_target}_${var.owner}",
    System = "t3_micro-centos8",
  }
```

>**Note:** host names in Ansible must be made of alphanumerical and underscore characters only. Although Terraform and AWS don't have that limitation, the Name tag needs to follow it so the connection between Terraform and Ansible can be made.


> Since an elastic IP address is being used, you might need to delete the entry
in the `~/.ssh/known_hosts` file associated with the elastic IP address if this
is not the first time you have performed this provisioning step. 

Ansible will update values for the project environment variables below in 
GitLab. Check them on the project environment variables page after the Ansible
provisioning has completed. This is done by the `docker-postinstall` role.

| Variable's Key | Environnments | Description |
|---|---|---|
| docker_tlsauth_ca | staging | Certificate authority for staging server - this is provided by the staging server during Ansible provisioning |
| docker_tlsauth_cert | staging | Public certificate for staging server - this is provided by staging server during Ansible provisioning |
| docker_tlsauth_key  | staging | the server key for the above CA - this is provided by staging server during Ansible provisioning |
| docker_tlsauth_ca | live | Certificate authority for staging server - this is provided by the live server during Ansible provisioning |
| docker_tlsauth_cert | live | Public certificate for staging server - this is provided by live server during Ansible provisioning |
| docker_tlsauth_key  | live | the server key for the above CA - this is provided by live server during Ansible provisioning |
| remote_public_ip    | staging | Public IP address of staging server |
| remote_private_ip   | staging | Private IP address of staging server |
| remote_public_ip    | live | Public IP address of live server |
| remote_private_ip   | live | Private IP address of live server |

This is for running a secure Docker engine on the AWS EC2 server
so that the Docker API is secured over TCP and we know we are communicating 
with the correct server and not a malicious impersonation. We also need to 
authenticate the client with TLS so only clients using the client certificates 
can use the Docker engine. This is the 2-way certificate-based authentication.

>When Ansible generates the client/server certificate, it writes them on the EC2 instance at location ``/home/centos/.docker/``

>If an operator needs to perform a docker action on the EC2 instance from this development machine,
the three files constituting the client certificates `ca.pem`, `cert.pem` and `key.pem` in `ops/infrastructure/envs/<DEPLOY_ENV>/output/` or from gitlab variables need to be copied to ``~/.docker/`` in development machine.
Then the containers in dockerhost server can be accessed like this:
```
docker --tlsverify -H=<remote_public_ip>:2376 ps
```

The RDS instance is provisioned with a database via the bastion server by a
separate ansible playbook:
```
$ cd ops/infrastructure/envs/staging
$ ansible-playbook -i ../../inventories bastion_playbook.yml
```

The bastion playbook will create a `gigadb` database containing data from
`sql/production_like.pgdmp`. This file is created by running `./up.sh` when
spinning up a local GigaDB on your development platform.

### Further configuration steps

The new gigadb-website code contains functionality for running GigaDB over 
[HTTPS](https://en.wikipedia.org/wiki/HTTPS). 
[Let's Encrypt](https://letsencrypt.org) is used as a trusted certificate 
authority to sign a certificate provided by GigaDB which is trusted by users.

For Let's Encrypt to do this, the server used for staging GigaDB requires a 
domain name as mentioned above. The EC2 domain names provided by AWS cannot be 
used because they are ephemeral and so are blacklisted by Let's Encrypt. Your 
own domain name must be used instead, *e.g.* [http://gigadb-staging.gigatools.net] 
which Let's Encrypt will verify as a domain name under your control.

Currently, an EC2 instance is used to host a GigaDB staging server. This EC2 
instance has been allocated an elastic IP address which should be mapped onto 
the domain name (*e.g.* [http://gigadb-staging.gigatools.net]) using your domain 
name registry service. This domain name is provided with an SSL certificate by 
Let's Encrypt.

The deployment of the certificate is handled by the ``ops/scripts/setup_cert.sh`` script which 
is summoned during a deployment job (``sd_gigadb`` or ``ld_gigadb``).

#### NGINX configuration

In addition, NGINX conf files need to be configured with the domain name of the 
target server (staging or live).
The GitLab variable that needs to be set for this is ``$REMOTE_HOSTNAME``.

As part of executing ``ops/scripts/generate_config.sh`` by running ``docker-compose run --rm config``, the templates: 

* ops/configuration/nginx-conf/sites/nginx.target_deployment.http.conf.dist
* ops/configuration/nginx-conf/sites/nginx.target_deployment.https.conf.dist

will be used to create the final Nginx site configs file with the value of ``$REMOTE_HOSTNAME``

at the in-container location ``/etc/nginx/sites-available/``

The script ``ops/configuration/nginx-conf/enable_sites`` will enable the sites whose configuration are passed as parameter.

Example from ``ops/pipelines/gigadb-deploy-jobs.yml``
```
- docker-compose --tlsverify -H=$REMOTE_WEBAPP_DOCKER -f ops/deployment/docker-compose.production-envs.yml exec -T web /usr/local/bin/enable_sites gigadb.$GIGADB_ENV.https
```

When the container service ``web`` is started, it will also execute that ascript to enable the default http configuration for Gigadb.org,
so that a TLS certificates created with Let's Encrypt can pass verification.

The TLS certificate for the domain $REMOTE_HOSTNAME are managed by the script ``ops/scripts/setup_cert.sh``

It will create new certificates with Let's encrypt if none exists on the target deployment or remotely as GitLab variables.
if existing on the target deployment, the script will request renewal which Let's Encrypt will only perform if close to expiration.

Upon creation of new certificate or renewal, the certificates are backed up to GitLab as the following variables:

| name | environment | description |
| --- | --- | --- |
| tls_fullchain_pem | staging | all certificates, including server certificate (aka leaf certificate or end-entity certificate). The server certificate is the first one in this file, followed by any intermediates. |
| tls_privkey_pem | staging | private key for the certificate in PEM format |
| tls_chain_pem | staging | contains the additional intermediate certificate or certificates that web browsers will need in order to validate the server certificate |
| tls_fullchain_pem | live | all certificates, including server certificate (aka leaf certificate or end-entity certificate). The server certificate is the first one in this file, followed by any intermediates. |
| tls_privkey_pem | live | private key for the certificate in PEM format |
| tls_chain_pem | live | contains the additional intermediate certificate or certificates that web browsers will need in order to validate the server certificate |

When provisioning a new staging or live environment, ``ops/scripts/setup_cert.sh`` will attempt to pull these variables if they exist.
This way, we won't unnecessarily creat certificate everytime we need to re-create a staging and deployment, 
thus reducing the risk of hitting weekly rate-limit for certificate creation imposed by Let's Encrypt.

### Restoration of database snapshots

```
# Go to environment directory
$ cd <path>/gigadb-website/ops/infrastructure/envs/staging

# Terminate existing RDS service
$ terraform destroy --target module.rds

# Restore database snapshot
$ terraform plan -var snapshot_identifier="snapshot-for-testing"
$ terraform apply -var snapshot_identifier="snapshot-for-testing"
$ terraform refresh
```

### Restoration of database backups
```
# Go to environment directory
$ cd <path>/gigadb-website/ops/infrastructure/envs/staging

# Terminate existing RDS service
$ terraform destroy --target module.rds

# Copy override.tf to staging environment
$ ../../../scripts/tf_init.sh --project gigascience/forks/pli888-gigadb-website --env staging --restore-backup

# Backups can either be restored to its latest restorable time or to a specific
# time.

# To restore to latest restorable time - need to override database name as this 
# will come from the backup
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var use_latest_restorable_time="true"

# To restore to specific time in backup - need to override database name as this 
# will come from the backup
$ terraform apply -var source_dbi_resource_id="db-6GQU4LWFBZI34AOR5BW2MEQFLU" -var gigadb_db_database="" -var utc_restore_time="2021-10-27T06:02:12+00:00"
```

### Troubleshooting

If you meet the error:
 ```
 Error: Error launching source instance: MissingInput: No subnets found for the default VPC 'vpc-a717e2ce'. Please specify a subnet.
 ```

Then running the following command to create a default subnet will fix it:

 ```
 aws ec2 --profile UserNameOfAdminUser create-default-subnet --region ap-east-1 --availability-zone ap-east-1a
 ```

All SQL statements executed on the PostgreSQL RDS instance are logged in staging
deployments of GigaDB. These `postgres.log` files are available from the AWS
console for the RDS service in the `Log & events` tab.
