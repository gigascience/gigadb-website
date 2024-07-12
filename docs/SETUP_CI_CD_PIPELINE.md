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

##### Variables for credentials

The following variables need to be set for Environment "All (default)"

| Name | Masked? |
| --- | --- |
| DOCKER_HUB_USERNAME | no |
| DOCKER_HUB_PASSWORD | yes |
| AWS_ACCESS_KEY_ID | yes |
| AWS_SECRET_ACCESS_KEY | yes |

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



