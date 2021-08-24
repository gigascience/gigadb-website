# How to set up CI/CD pipelines on gitlab.com

Application development may involve implementing small code changes which are 
frequently checked into version control. Continuous Integration (CI) provides a 
consistent and automated way to build, package and test the application under 
development. Furthermore, Continuous Delivery (CD) automates the deployment of 
applications to specific infrastructure environments such as staging and 
production servers.

## Use of GitLab for Continuous Integration

GitLab provides a CI service used by GigaDB. The CI/CD pipeline is described in 
the [`.gitlab-ci.yml`](https://github.com/gigascience/gigadb-website/blob/develop/.gitlab-ci.yml)
file located at the root of the repository. A Runner in GitLab triggers the CI 
pipeline every time there is a code commit or push. GitLab.com allows you to use 
Shared Runners provided by GitLab Inc which are virtual machines running on 
GitLab's infrastructure to build any project.

The GigaDB `gitlab-ci.yml` configuration file tells the GitLab Runner to run a pipeline job 
with these stages:
- build for test
- test
- conformance and security
- production build
- staging deploy
- live deploy

That file is the entry point for configuring the GitLab pipelines. The configuration is organised in a modular way.
Thus, ``gitlab-ci.yml`` includes other configuration files to maintain a clear organisation:

```
ops/pipelines/
├── gigadb-build-jobs.yml #build jobs for CI and production go here
├── gigadb-conformance-security-jobs.yml #jobs that check for vulnerabilites and conformance to coding guidelines
├── gigadb-deploy-jobs.yml #jobs for deploying to production environments (staging and live)
├── gigadb-operations-jobs.yml #jobs for utilities and convenience for operating/debugging the pipelines
└── gigadb-test-jobs.yml #jobs for running tests as part of continuous integration
```

The above steps support testing and deployment of GigaDB, but assumes that the 
set up of the Docker server is already done separately.

### Mirroring your forked gigadb-website repository from GitHub

To begin, mirror your forked GitHub gigadb-website repository as a GitLab 
project. This is done by adding your GitHub gigadb-website repository to the 
GitLab Gigascience Forks organisation. To do this:

* Log into GitLab and go to the 
[gigascience/Forks page](https://gitlab.com/gigascience/forks).
 
* Click on *New Project* followed by *CI/CD for external repo* and then 
*GitHub*. 

* On the *Connect repositories from GitHub page*, click on the 
*List your GitHub repositories* green button. Find the repository fork of 
`gigadb-website` that you want to perform CI/CD on.

* Under the *To GitLab* column, select *gigascience/forks* to connect your repo 
to this GitLab group. Also, provide a name for the repo, *e.g.* 
`pli888-gigadb-website` so that you can differentiate your repository from 
others in the Forks group.

* Click the *Connect* button to create the mirror of the GitHub repository on
GitLab.

### Configuring your GitLab gigadb-website project

Your new GitLab `gigadb-website` project requires configuration:

* The default branch needs to be selected for your project to allow you to 
perform CI/CD on this branch. Go to the Repository settings for your project, 
*e.g.*
[https://gitlab.com/gigascience/forks/pli888-gigadb-website/settings/repository],
 click on the *Expand* button for the `Default Branch` settings. Use the 
drop-down menu to select the default branch and click the *Save changes* green 
button. Whatever branch you select requires a .gitlab-ci.yml file at the root of 
the repository project for CI/CD to work.

* Go to the CI/CD Settings for your project, *e.g.*
[https://gitlab.com/gigascience/forks/pli888-gigadb-website/settings/ci_cd]. In 
the *General pipelines* section, ensure that the *Public pipelines* checkbox is 
**NOT** ticked, otherwise variables will leak into the logs. The 
*Test coverage parsing* text field should also contain: 
` \ \ Lines:\s*(\d+.\d+\%)`. Click on the *Save changes* green button.
 
* The variables below need to be created for your project in the `Environment variables` 
section in the CI/CD Settings page. Any values below listed as `somevalue` 
should be replaced by proper values - please contact the GigaScience tech 
support team for help with setting these.
  

These environment variables together with those in the Forks group are exported 
to the `.secrets` file and are listed 
[here](https://github.com/gigascience/gigadb-website/blob/develop/ops/configuration/variables/secrets-sample). 
All these GitLab CI/CD environment variables are referred to in the 
`gitlab-ci.yml` file or used in the CI/CD pipeline.

Variable Name | Value
------------- | -----
ANALYTICS_CLIENT_EMAIL | somevalue
ANALYTICS_CLIENT_ID | somevalue
ANALYTICS_PRIVATE_KEY | somevalue
COVERALLS_REPO_TOKEN | somevalue
FORK | somevalue
MAILCHIMP_API_KEY | somevalue
MAILCHIMP_LIST_ID | somevalue
MAILCHIMP_TEST_EMAIL | somevalue


### Executing a Continuous Integration run
 
Your CI/CD pipeline can now be executed:

* Go to your pipelines page and click on *Run Pipeline*.

* In the *Create for* text field, confirm the name of the branch you want to run 
the CI/CD pipeline. The default branch should already be pre-selected for you. 
Then click on the *Create pipeline* button. 

* Refresh the pipelines page, you should see the CI/CD pipeline running. If the 
set up of your pipeline is successful, you will see it run the build, test, 
security and conformance stages defined in the `.gitlab-ci.yml` file.
 
## Continuous Deployment in the CI/CD pipeline

The deployment of `gigadb-website` code onto a staging or live servers to 
provide a running GigaDB application is not automatically performed by the 
CI/CD pipeline since it is set to run manually in the `.gitlab-ci.yml` file. 
This part of the CI/CD process has to be explicitly executed from the GitLab 
pipelines page.

Prior to this, a host machine has to be instantiated with a 
secure Docker daemon on which the GigaDB application will be deployed. This 
machine can be used for a specific environment, most likely staging or 
live. 

There are two pre-requisites to fulfill before. First, GitLab needs be configured for build and deployment to production (staging and live).
Second, several tools are needed to set up a Docker-enabled server on the AWS cloud: 
AWS-CLI, Terraform, and Ansible.

### Preparing GitLab for provisioning, build and deployment

environmments are the foundation. There are two types of environments: developement and production.

| Environment name | Type | Purpose |
| --- | --- | --- |
| dev | development | a developer's local development machine where they create applications |
| CI | development | an environment created and hosted on GitLab to run automated tests continuously upon every commits (Continuous Integration) |
| staging | production | an environmment hosted on AWS cloud for final acceptance of a version of the web site product that's like the real live in every aspect |
| live | production | the real live web site product hosted on AWS cloud |

Since the local environment is on the developer's machine, it is not our concern in this section.
The CI environment is implictely created by the CI part of GitLab and is concerned with our present topic
only as a pre-requisite: deployment to staging and live can only happen if the tests pass in CI.

``staging`` and ``live`` are what  matter in this section. If they are not already, these environments need to  created
in GitLab under the ``Deployments > Environments`` section.
They have two very important use:

 * the variables we need to configure the services and applications we deploy with need to be specific to each environment. GitLab allows
us to store variables and specify for which environment this variable is bound to (for organisation and security).
 * Gitlab stages and jobs must be tied to a specific environment, so that pipelines don't leak variables.

#### GitLab Variables

Ensure the following variables are set for their respective environments in the appropriate GitLab project (the variables should have the Protected checkbox unchecked if it's your personal project space in the Forks group)

| Name | Masked? |
| --- | --- |
| DEPLOYMENT_ENV | no |
| REMOTE_HOME_URL | no |
| REMOTE_HOSTNAME | no |
| REMOTE_PUBLIC_HTTP_PORT | no |
| REMOTE_PUBLIC_HTTPS_PORT | no |
| REMOTE_SMTP_HOST | no |
| REMOTE_SMTP_PASSWORD | yes |
| REMOTE_SMTP_PORT | no |
| REMOTE_SMTP_USERNAME | no |
| gigadb_db_host | no |
| gigadb_db_user | no |
| gigadb_db_password | yes |
| gigadb_db_database | no |
| fuw_db_host | no |
| fuw_db_user | no |
| fuw_db_password | yes |
| fuw_db_database | no |

so, there should be two versions of each variable, one for each environment.

##### Good examples:

| Key | Value | Masked | Environments |
| --- | --- | --- | --- |
| DEPLOYMENT_ENV | staging | x | staging|
| DEPLOYMENT_ENV | live | x | live|
| gigadb_db_password| 1234 | v | staging |
| gigadb_db_password| 5678 | v | live |


##### Bad examples:

| Key | Value | Masked | Environments |
| --- | --- | --- | --- |
| DEPLOYMENT_ENV | live | x | All (default) |
| gigadb_db_host | dockerhost | x | staging |


#### Jobs and stages in GitLab configuration files

Every job defined in the configuration need to have their stage and environment specified.
The former enables the execution order of the pipeline, and the latter ensures the variables for the selected 
environment only is made available to the pipeline's jobs.

> The name of valid stages to be used in GitLab configuration are listed at the top of the file ``.gitlab-ci.yml``  

> Ensure the value of ``environment:name:`` in GitLab configuration matches the environment that you have created in Gitlab dashboard under ``Deployments > Environments``


##### Examples:

 * from the ``ops/pipelines/gigadb-build-jobs.yml`` file:
```
build_live:
  variables:
    GIGADB_ENV: "live"
  extends: .pb_gigadb
  stage: production build
  environment:
    name: "live"
    deployment_tier: production
    url: $REMOTE_HOME_URL
```
 
 * from the ``.gitlab-ci.yml`` file:
```
sd_gigadb:
  variables:
    GIGADB_ENV: "staging"
  extends: .deploy
  stage: staging deploy
  environment:
    name: "staging"
    url: $REMOTE_HOME_URL
    on_stop: sd_teardown
```


### Tools

#### AWS-CLI

TODO: link up to @pli888's aws docs

#### Terraform

[Terraform](https://www.terraform.io) is a tool which allows you to describe and
instantiate infrastructure as code.

Install Terraform-0.14 by downloading the installer from the 
[Terraform](https://www.terraform.io) web site or it can be installed using a 
package manager for your operating system. For example, macOS users can use 
[Macports](https://www.macports.org) or [HomeBrew](https://brew.sh/):
```
$ sudo port install terraform-0.14
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

>Ensure you download version 0.14+ of Terraform as the code make use of syntax not available to versions before that.

##### List of modules:

| Path | Purpose | Pre-requisites |
| --- | --- | --- |
| ops/infrastructure/modules/aws-instance | deploy an EC2 instance and associated root volume, security group and Elastic IP linking | Ask your admin to create an Elastic IP for your deployment |
| ops/infrastructure/modules/rds | deploy a PostgreSQL RDS service and associated security group | DB credentials defined in GitLab variables | 

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
$ ../../../scripts/tf_init.sh --project gigascience/forks/rija-gigadb-website --env environment
```

where we replace ``gigascience/forks/rija-gigadb-website`` with the appropriate GitLab project.
and ``environment`` with ``staging`` or ``live``

The script also copies the ``ops/infrastructure/terraform.tf`` root module in the environment-specific directory so 
that terraform commands can be run from that directory.

##### Environment specific directories

* For staging: ``cd ops/infractructure/envs/staging``
* For live: ``cd ops/infractructure/envs/live``

Those directories start empty, but the ``ops/scripts/tf_init.sh`` aforementioned and ``ops/scripts/ansible_init.sh`` below will populate them with necessary files
so we can run ``terraform`` and ``ansible-playbook`` commands from those directories for a safe provisioning of the desired environment.

In particular, the ``ops/scripts/tf_init.sh`` script will write in a ``.init_vars`` file the answer to the prompted value, so that they are not asked
again in subsequent runs. ``ops/scripts/ansible_init.sh`` will also source that file.

#### Ansible

[Ansible](https://www.ansible.com) is used to install the EC2 instance 

with a Docker daemon. In addition, a PostgreSQL server is installed on the EC2
instance which will host the database that GigaDB uses to manage information
abouts its datasets. Note that this setup for a staging instance of GigaDB is
different to a local GigaDB application whose PostgreSQL database is provided by
a custom Docker container.

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
[name_gigadb_server_staging] # host name for staging deployment, the name is a concatenation of AWS tag key and value attached to the EC2 instance

# do not add any IP address here as it is dynamically managed using terraform-inventory

[name_gigadb_server_live] # host name for live deployment, the name is a concatenation of AWS tags attached to the EC2 instance

# do not add any IP address here as it is dynamically managed using terraform-inventory

[all:vars] # host variables needed by Ansible to configure software and services. Most have their value pulled from ansible.properties created with the ansible_init.sh script

gitlab_url = "https://gitlab.com/api/v4/projects/{{ lookup('ini', 'gitlab_project type=properties file=ansible.properties') | urlencode | regex_replace('/','%2F') }}"
ansible_ssh_private_key_file = "{{ lookup('ini', 'ssh_private_key_file type=properties file=ansible.properties') }}"
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

```

>Note that the header **[name_gigadb_staging]** must match the "Name" tag associated to the AWS EC2 resource defined in ``ops/infrastructure/modules/aws-instance/aws-instance.tf`` for the environment of interest (here ``staging``):

```
tags = {
    Name = "gigadb_server_${var.deployment_target}",
    Hosting = "ec2-as1-t2m-centos"
    Environment = "staging"
    Owner = "Rija"
 }
```

>**Note:** host names in Ansible must be made of alphanumerical and underscore characters only. Although Terraform and AWS don't have that limitation, the Name tag needs to follow it so the connection between Terraform and Ansible can be made.

An ``ansible.properties`` file needs to exist in the environment-specific directory for a given environment.
This file is queried by the host variables shown above, and is created using the ``ops/scripts/ansible_init.sh``:

```
$ cd ops/infrastructure/envs/environment
$ ../../../scripts/ansible_init.sh --env environment
```
Where environment must be replaced by ``staging`` or ``live``.

That script needs to be executed after ``ops/scripts/tf_init.sh`` has been run, as our script is dependent
on the existence of an ``.init_env_vars`` file created by the latter.

That script also makes a copy of the ``ops/infrastructure/playbook.yml`` into the environment-specific directory
so the playbook can be performed from environment specific directory.

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
$ ansible-playbook -i ../../inventories -i name_gigadb_staging  playbook.yml
```

> Since an elastic IP address is being used, you might need to delete the entry
in the `~/.ssh/known_hosts` file associated with the elastic IP address if this
is not the first time you have performed this provisioning step. 

Ansible will update values for the project environment variables below in 
GitLab. Check them on the project environment variables page after the Ansible
provisioning has completed. This is done by the `docker-postinstall` role.

| Environment variable | Description |
|----------------------|-------------|
| staging_tlsauth_ca | Certificate authority for staging server - this is provided by the staging server during Ansible provisioning |
| staging_tlsauth_cert | Public certificate for staging server - this is provided by staging server during Ansible provisioning |
| staging_tlsauth_key  | the server key for the above CA - this is provided by staging server during Ansible provisioning |
| staging_public_ip    | Public IP address of staging server |
| staging_private_ip   | Private IP address of staging server |

 
This is for running a secure Docker engine on the production CNGB virtual server
so that the Docker API is secured over TCP and we know we are communicating 
with the correct server and not a malicious impersonation. We also need to 
authenticate the client with TLS so only clients using the client certificates 
can use the Docker engine. This is the 2-way certificate-based authentication.

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
staging server. Edit the following two NGINX conf files so that any mention of 
`gigadb-staging.gigatools.net` is replaced by the domain name you are using to 
stage GigaDB.

* `ops/configuration/nginx-conf/sites/gigadb.staging.http.conf`
* `ops/configuration/nginx-conf/sites/gigadb.staging.https.conf`

Commit these two NGINX conf files into your repository. You should see a new 
CI/CD pipeline process start and end with a successful build. 

### Executing the CD pipeline for deployment

#### 1. Choose the environment for which to deploy

* For staging: ``cd ops/infractructure/envs/staging``
* For live: ``cd ops/infractructure/envs/live``

Those directories start empty, but the ``tf_init.sh`` and ``ansible_init.sh`` scripts below will populate them with necessary files
so we can run ``terraform`` and ``ansible-playbook`` commands from those directories for a safe provisioning of the desired environment.

#### 2. Initialise Terraform

```
$ ../../../scripts/tf_init.sh --project gigascience/forks/rija-gigadb-website --env environment
```

where you replace ``gigascience/forks/rija-gigadb-website`` with the appropriate GitLab project.
and ``environment`` with ``staging`` or ``live``

#### 3. Initialise Ansible

```
$ ../../../scripts/ansible_init.sh --env environment
```

where you replace ``environment`` with ``staging`` or ``live``

#### 4. Provision with  Terraform and perform Ansible playbook

Ensure you are still in ``ops/infractructure/envs/staging`` or ``ops/infractructure/envs/live``

```
$ pwd
$ terraform plan
$ terraform apply
$ terraform refresh
$ ansible-playbook -i ../../inventories -i name_gigadb_server_environment playbook.yml
```
where you replace ``environment`` with ``staging`` or ``live``

when performing the plays, It's important to pay particular attention  to the selected host group after the ``-i`` parameter.

The two valid values are listed in ``ops/infrastructure/inventories/hosts`` as section heads ``[...]``

That latter file also shows how the files created in the environment-specific directory by ``tf_init.sh`` and ``ansible_init.sh`` are used to set the Ansible host variables.
There's no need to use ``ansible-vault`` anymore.

> Since an elastic IP address is being used, you might need to delete the entry
in the `~/.ssh/known_hosts` file associated with the elastic IP address if this
is not the first time you have performedthe plays in the playbook.

#### 5. Build and deploy

Use the Gitlab Pipelines dashboard to build the application (by triggering manual job ``build_staging`` or ``build_live``).
Once the production containers are build (in the Gitlab Containers Registry they are listed as production_<service>:<environment),
We can proceed with deployment to the target environment by triggering manual job ``sd_gigadb`` (staging) or ``ld_gigadb`` (live).

The application should be available at the url defined in $REMOTE_HOME_URL for a given environment.


### Troubleshooting

If you meet the error:
 ```
 Error: Error launching source instance: MissingInput: No subnets found for the default VPC 'vpc-a717e2ce'. Please specify a subnet.
 ```

Then running the following command to create a default subnet will fix it:

 ```
 aws ec2 --profile UserNameOfAdminUser create-default-subnet --region ap-east-1 --availability-zone ap-east-1a
 ```