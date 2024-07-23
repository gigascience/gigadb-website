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

### Understanding environments

Environmments are the foundation of the pipeline.
There are used in two contexts:
* When getting and setting variables
* When deploying the code 


#### When deploying the code

There are two types of environments: development and production.

| Environment name | Type | Purpose |
| --- | --- | --- |
| dev | development | a developer's local development machine where they create applications |
| CI | development | an environment created and hosted on GitLab to run automated tests continuously upon every commits (Continuous Integration) |
| staging | production | an environmment hosted on AWS cloud for final acceptance of a version of the web site product that's like the real live in every aspect |
| live | production | the real live web site product hosted on AWS cloud |

The local environment is on the developer's machine, that's what the dev environment refers to. 
The CI environment is implictely created by the CI part of GitLab, that's where the code is deployed for the execution of the automated tests.
The CI is a gate-keeper for the production environments: deployment to staging and live can only happen if the tests pass in CI.

#### When getting and settings variables from Gitlab

A functionality of Gitlab is to store environment variables, so that we can use them in our deployed applications.
Because there is multiple deployument environments and the variables often differ from one to the other, Gitlab variables can be categorised and into different environment which are:
* dev
* staging
* live
* All (or \*)

**Note:** there is no connection between the environment of variables and the deployment environments listed in previous section. By convention the `staging` and `live` environments for variables are associated with the `staging` and `live` deployments respectively. (i.e: a staging variable is only to be used on staging deployment environment, and a live variable is to be used only on live deployment environment).
`All` class of variables are needed in applications regardless of their deployment environments, while the `dev` class of variables are equally used on a developer's local environments and on CI deployment environment.

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
section in the CI/CD Settings page.   
Make sure the "Protect variable" and "Expand variable reference" checkboxes are unchecked.
the Visibility radio input should be set to "Visible" except for the passwords and tokens that should be set to "Masked".

| Variable Name          | Value     | Environment |
|---|---|---|
| DOCKER_HUB_USERNAME    | Your login on Docker hub | All |
| DOCKER_HUB_PASSWORD    | Your password on Docker hub | All |
| COVERALLS_REPO_TOKEN   | Ask tech team | All |
| GIGADB_HOST | database | dev |
| GIGADB_USER | gigadb | dev |
| GIGADB_PASSWORD | Pick one | dev |
| GIGADB_DB | gigadb | dev |
| FUW_DB_HOST | database | dev |
| FUW_DB_USER | fuwdb | dev |
| FUW_DB_PASSWORD | Pick one | dev |
| FUW_DB_NAME |fuwdb |  dev |
| REVIEW_DB_HOST | reviewdb | dev |
| REVIEW_DB_USERNAME | reviewdb | dev |
| REVIEW_DB_PORT | 5432 | dev |
| REVIEW_DB_PASSWORD | Pick one | dev |
| REVIEW_DB_DATABASE | reviewdb | dev |
| GITLAB_PRIVATE_TOKEN | Ask tech team | All |

Those environment variables together with those in the Forks group are exported 
to the `.secrets` file and are listed 
[here](https://github.com/gigascience/gigadb-website/blob/develop/ops/configuration/variables/secrets-sample). 
All these GitLab CI/CD environment variables are referred to in the 
`gitlab-ci.yml` file or used in the CI/CD pipeline.


### Executing a Continuous Integration run
 
Your CI/CD pipeline can now be executed up to and including the **test** stage:

* Go to your pipelines page and click on *Run Pipeline*.

* In the *Create for* text field, confirm the name of the branch you want to run 
the CI/CD pipeline. The default branch should already be pre-selected for you. 
Then click on the *Create pipeline* button. 

* Refresh the pipelines page, you should see the CI/CD pipeline running. 
If the set up of your pipeline is successful, you will see it run the build, test, 
security and conformance stages defined in the `.gitlab-ci.yml` file.
 
## Continuous Deployment in the CI/CD pipeline

The deployment of `gigadb-website` code onto a staging or live servers to 
provide a running GigaDB application is not automatically performed by the 
CI/CD pipeline since it is set to run manually in the `.gitlab-ci.yml` file. 
This part of the CI/CD process has to be explicitly executed from the GitLab 
pipelines page.

Prior to this, a host machine has to be instantiated with a secure Docker daemon
on which the GigaDB application will be deployed. In addition, an RDS machine 
is created to provide a PostgreSQL database for GigaDB. Both these machines can
be used for a specific environment, most likely staging or live.

There are two pre-requisites to fulfill before. First, GitLab needs be configured for build and deployment to production (staging and live).
Second, several tools are needed to set up a Docker-enabled server on the AWS cloud: 
AWS-CLI, Terraform, and Ansible.

### Preparing GitLab for provisioning, build and deployment

``staging`` and ``live`` are what  matter in this section. If they are not already, these environments need to  created
in GitLab under the ``Deployments > Environments`` section.
They have two very important use:

 * the variables we need to configure the services and applications we deploy with need to be specific to each environment. GitLab allows
us to store variables and specify for which environment this variable is bound to (for organisation and security).
 * Gitlab stages and jobs must be tied to a specific environment, so that pipelines don't leak variables.

#### Create DNS record for accessing endpoint on staging and on live servers

There is a couple of endpoints that need to have a domain name associated with them for each deployment environments.

The domain names should be for staging enviroment:
* yoursubdomain-staging.gigadb.host
* portainer.yoursubdomain-staging.gigadb.host
and optionally:
* files.yoursubdomain-staging.gigadb.host
* bastion.yoursubdomain-staging.gigadb.host

and for live environment:
* yoursubdomain-live.gigadb.host
* portainer.yoursubdomain-live.gigadb.host
and optionally:
* files.yoursubdomain-live.gigadb.host
* bastion.yoursubdomain-live.gigadb.host

where *yourdomain* is a unique short string of your choice to identify your endpoints form those of other team members, like your IAM role name in lowercase or Gitlab project prefix

Ask a core team member to create an "A" record in the DNS server to map to the Elastic IPs you have set up in previous section for your staging and live environment.


#### AWS dashboard

There are three activities to perform on the AWS dashboard's EC2 console prior to using the GitLab Pipeline for deployment:

1. Creation of Elastic IPs (under ``Network & Security > Elastic IPs``) to be used for the deployments to ``staging`` and ``live`` environments
1. Creation of a SSH Key Pair (under ``Network & Security > Key Pairs``) that will allow Ansible and operators to ssh into the deployed EC2 instances
1. Creation of API keys (under top-right dashboard menu item `<IAM Role Username> @  <AWS account ID> > Security Credentials`, then click `Create access key`, the click the button to download the `.CSV` file)

The first two resources needs to be globally unique in the same AWS acccount, so you need to follow the naming convention below:

1. For Elastic IPS: ``eip-<application>-<environment>[-<sub-system>]-<IAM Role Username>``, e.g: ``eip-gigadb-staging-John`` or ``eip-gigadb-files-staging-John``
1. For SSH Key pair: ``aws-<application>-<AWS region>-<IAM Role Username>.pem``, e.g: ``aws-gigadb-eu-north-1-John.pem``
The private part of the SSH Key pair needs to be dowloaded to your developer machine in the ``~/.ssh`` directory and with 
permission set to ``600``.

Here the 3 EIPs you must create for provisioning a staging environment:

EIPs Name tag | associated domain, if any |
| -- | -- |
| ``eip-gigadb-staging-<IAM Role Username>`` | yoursubdomain-staging.gigadb.host |
| ``eip-gigadb-bastion-staging-<IAM Role Username>`` | (optional)bastion.yoursubdomain-staging.gigadb.host |
| ``eip-gigadb-files-staging-<IAM Role Username>`` | (optional)files.yoursubdomain-staging.gigadb.host |
 
If also deploying to a live environment, you will need to create

EIPs Name tag | associated domain (only the 1st one is mandatory) |
| -- | -- |
| ``eip-gigadb-live-<IAM Role Username>`` | yourdomain-live.gigadb.host |
| ``eip-gigadb-bastion-live-<IAM Role Username>`` | (optional)bastion.yoursubdomain-live.gigadb.host |
| ``eip-gigadb-files-live-<IAM Role Username>`` | (optional)files.yoursubdomain-live.gigadb.host |

**Notes**: By default the number of EIPs allowed to be created in any given region is limited to 5. So if you need to deploy a live environment, you will need to request a quota increase for the region you are deploying into. Ask a core team member to do it for you.

##### AWS credentials

The credentials obtained from the AWS dashboard needs to be stored locally at the path `~/.aws/credentials` under a profile called `[gigadb]`

if the file doesn't exist yet, it should looks something like:

```
[gigadb]
aws_access_key_id=XXXXX
aws_secret_access_key=YYYYY
```

#### GitLab Variables

Ensure the following variables are set for their respective environments in the appropriate GitLab project.
Make sure the "Protect variable" and "Expand variable reference" checkboxes are unchecked.
the Visibility radio input should be set to "Visible" except for the passwords and tokens that should be set to "Masked".

| Name | value | 
| --- | --- |
| DEPLOYMENT_ENV | deployment environment goes here |
| REMOTE_HOME_URL | URL to the home website as https://FQDN |
| REMOTE_HOSTNAME | domain name associated to the elastic IP of the web server as FQDN |
| REMOTE_PUBLIC_HTTP_PORT | 80 |
| REMOTE_PUBLIC_HTTPS_PORT | 443 |
| REMOTE_SMTP_HOST | Pick an SMTP host |
| REMOTE_SMTP_PASSWORD | SMTP password |
| REMOTE_SMTP_PORT | 563 |
| REMOTE_SMTP_USERNAME | SMTP username |
| gigadb_db_host | keep empty, it will be overwritten by the provising script with RDS endpoint |
| gigadb_db_user | gigadb |
| gigadb_db_password | Pick a password |
| gigadb_db_database | gigadb |
| fuw_db_host | keep empty, it will be overwritten by the provisioning script with RDS endpoint |
| fuw_db_user | fuw |
| fuw_db_password | Pick a password |
| fuw_db_database | fuw |
| REVIEW_DB_DATABASE | reviewdb |
| REVIEW_DB_PASSWORD | Pick a password |
| REVIEW_DB_PORT | 5432 |
| REVIEW_DB_USERNAME | reviewdb |
| REVIEW_DB_HOST | reviewdb |
| PORTAINER_PASSWORD | Pick a password |
| remote_fileserver_hostname | files.yoursubdomain.gigadb.host | 
| REMOTE_PUBLIC_HTTP_PORT | 80 |
| REMOTE_PUBLIC_HTTPS_PORT | 443 |
| 
so, there should be 2 versions of each variable, one for each deployment environment (staging or live).

##### Good examples:

| Key | Value | Masked | Environments |
| --- | --- | --- | --- |
| DEPLOYMENT_ENV | staging | x | staging|
| DEPLOYMENT_ENV | live | x | live|
| gigadb_db_password| 1234 | v | staging |
| gigadb_db_password| 5678 | v | live |
| PORTAINER_PASSWORD | "password for staging" | v | staging |
| PORTAINER_PASSWORD | "password for live" | v | live |

##### Bad examples:

| Key | Value | Masked | Environments |
| --- | --- | --- | --- |
| DEPLOYMENT_ENV | live | x | All (default) |
| gigadb_db_host | dockerhost | x | staging |

##### Optional

The following variables can be configured as Gitlab Variables (or in .env) like above
but as they already have default values, one needs to change their values only if want
to depart from the default.

| Key                 | Role                                      | Default on Dev/CI | Default on Staging | Default on Live |
|---------------------|-------------------------------------------|-------------------|--------------------|-----------------| 
| YII_DEBUG           | enable debug mode for extra logging       | true              | true               | false           |
| YII_TRACE_LEVEL     | how many lines of context for log entries | 3                 | 0                  | 0               | 
| DISABLE_CACHE       | whether to disable caching of DB queries  | false             | false              | false           |
| SEARCH_RESULT_LIMIT | Nb. of results per page                   | 10                | 10                 | 10              |

>Note: the value of each of the first three variables has impact on website performances. 
> The default values for the live environment offer the maximum performance. 
> While the default values for Dev/CI provide the most debugging information.

>Note: those three variables set the values for PHP constants of the same names that are
> defined in the Yii web application's ``index.php`` file (generated from templates  ``ops/configuration/yii-conf/index.$GIGADB_ENV.php.dist``)

> Note: Although caching is on by default for all environments, but DISABLE_CACHE variable will be still available to provide flexibility if some specific development work needs it off.
>  DISABLE_CACHE can be manually configured to true in .env to turn off caching in dev environment.

##### Variables for configuring PHP-FPM

Below are further variables that must be set in Gitlab variables.
They are necessary to configure PHP-FPM application server.


| name | value | environment |
| -- | -- | -- |
| PHP_APCU_MEMORY | 128M | staging |
| PHP_FPM_MAX_CHILDREN | 17 | staging |
| PHP_FPM_START_SERVERS | 4 | staging |
| PHP_FPM_MIN_SPARE_SERVERS | 4 | staging |
| PHP_FPM_MAX_SPARE_SERVERS | 12 | staging |
| PHP_CONN_LIMIT | disabled | staging |

##### Exceptions

The following variables need to be set for Environment "All (default)"

| Name | Masked? |
| --- | --- |
| DOCKER_HUB_USERNAME | no |
| DOCKER_HUB_PASSWORD | yes |
| AWS_ACCESS_KEY_ID | yes |
| AWS_SECRET_ACCESS_KEY | yes |


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


>Note: Make sure you have a Docker Hub account and that its username and access token (which can be created in Docker Hub's security settings)
> are used as value for GitLab variables DOCKER_HUB_USERNAME and DOCKER_HUB_PASSWORD (set for the "All (default)" environment)
> as the ``before_script`` section of ``.gitlab-ci.yml`` uses them to login to Docker Hub and pull the main base image to speed up the build stage

#### Relationship between pipeline jobs and Ansible playbooks

The continuous integration part of the Gitlab pipeline is not dependent on the AWS deployed infrastructure and can be run anytime.
The provisioning of a new AWS infrastructure and deployment of Gigadb applicaiton onto it requires orchestration between the Gitlab pipeline and the operators.
There are 5 phases:

| phase | description | operator actions | required Gitlab pipeline state |
| -- | -- | -- | -- |
| TF init | The local terraform state is initiliased from a remote Gitlab state (which is created on the fly if it doesn't exist) | runs `tf_init.sh` | no state required |
| TF provisioning | The resources of the infrastructure are created on AWS | runs `terraform plan`, `terraform apply` and `terrafrom refresh` | no state required | 
| Ansible init | the AWS resources details are uploaded to Gitlab variables, Ansible dependency are built, and playbook copied to the deployment directory | runs `ansible_init.sh`  |no state required |
| Ansible provisioning | Configuration of the various GigaDB subsystems on each hosts of the infrastructure | executes the Ansible playbook | the build stage for the target environment should have run successfully | 
| Application deployment | deployment of the application code from git repository | none on staging, manually trigger Gitlab deployment stage on live | the deployment stage the target environment has been triggered and ran successfully |

**>Note:** The above implies that you cannot run the Ansible playbooks until you have run the build stage of the Gitlab pipeline, 
but you cannot run the build stage until you have run `ansible_init.sh`

### Acceptance tests

the stage between staging and live in Gitlab pipeline is for acceptance tests.
The rationale is that nothing gets deployed to live if acceptance tests are failing.
Only when the acceptance tests are passing that the jobs in the live stage of the pipeline become actionable.

The following Gitlab variables are needed for the acceptance run, both in the pipeline but also locally

| name | value | environment |
| -- | -- | -- |
| SERVER_EMAIL | foo@bar.local | dev |
| AWS_ACCESS_KEY_ID | your access key to AWS | All |
| AWS_SECRET_ACCESS_KEY | your secret key to AWS | All |


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
| ops/infrastructure/modules/bastion-aws-instance | deploy an EC2 instance to act as a bastion server for accessing RDS service |  |

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
# To check whether bastion server is accessible by loggin in
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

### Executing the CD pipeline for deployment

#### 1. Choose the environment for which to deploy

* For staging: ``cd ops/infractructure/envs/staging``
* For live: ``cd ops/infractructure/envs/live``

Those directories start empty, but the ``tf_init.sh`` and ``ansible_init.sh`` scripts below will populate them with necessary files
so we can run ``terraform`` and ``ansible-playbook`` commands from those directories for a safe provisioning of the desired environment.

#### 2. Initialise and provision with Terraform

```
$ ../../../scripts/tf_init.sh --project gigascience/forks/rija-gigadb-website --env environment
You need to specify the path to the ssh private key to use to connect to the EC2 instance: ~/.ssh/id-rsa-aws.pem
You need to specify your GitLab username: pli888
You need to specify a backup file created by the files-url-updater tool: ../../../../gigadb/app/tools/files-url-updater/sql/gigadbv3_20210929_v9.3.25.backup
# Now provision with Terraform
$ terraform plan  
$ terraform apply
$ terraform refresh
```

where you replace ``gigascience/forks/rija-gigadb-website`` with the appropriate GitLab project.
and ``environment`` with ``staging`` or ``live``

>To provision infrastructure for *.gigadb.org, we need to use the `Gigadb` AWS
> IAM user account which needs to be correctly configured in ~/.aws.credentials
> and ~/.aws/config. This IAM profile can then be used as follows:
```
$ AWS_PROFILE=Gigadb terraform plan
$ AWS_PROFILE=Gigadb terraform apply
$ AWS_PROFILE=Gigadb terraform refresh
```

#### 3. Initialise Ansible

```
$ ../../../scripts/ansible_init.sh --env environment
```

where you replace ``environment`` with ``staging`` or ``live``

#### 4. Perform Ansible playbook

Ensure you are still in ``ops/infractructure/envs/staging`` or ``ops/infractructure/envs/live``

```
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories webapp_playbook.yml -e="gigadb_env=environment"
$ env TF_KEY_NAME=private_ip OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook  -i ../../inventories files_playbook.yml -e="gigadb_env=environment" --tags="efs-mount-points"
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml -e "gigadb_env=environment" -e "backupDate=latest"
```
where you replace ``environment`` with ``staging`` or ``live``

when performing the plays, It's important to pay particular attention  to the selected host group after the ``-i`` parameter.

The two valid values are listed in ``ops/infrastructure/inventories/hosts`` as section heads ``[...]``

That latter file also shows how the files created in the environment-specific directory by ``tf_init.sh`` and ``ansible_init.sh`` are used to set the Ansible host variables.
There's no need to use ``ansible-vault`` anymore.

> Since an elastic IP address is being used, you might need to delete the entry
in the `~/.ssh/known_hosts` file associated with the elastic IP address if this
is not the first time you have performed the plays in the playbook.

#### 5. Build and deploy

Use the Gitlab Pipelines dashboard to build the application (by triggering manual job ``build_staging`` or ``build_live``).
Once the production containers are build (in the Gitlab Containers Registry they are listed as production_<service>:<environment),
We can proceed with deployment to the target environment by triggering manual job ``sd_gigadb`` (staging) or ``ld_gigadb`` (live).

The application should be available at the url defined in $REMOTE_HOME_URL for a given environment.

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
