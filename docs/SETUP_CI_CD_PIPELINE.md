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

The GigaDB `gitlab-ci.yml` file tells the GitLab Runner to run a pipeline job 
with these stages:
* build
* test
* security
* conformance
* staging
* live

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
should be replaced with proper values - please contact the GigaScience tech 
support team for help with setting these.

* The value of the STAGING_HOME_URL variable should be the domain name of the
machine you will use as the GigaDB staging server.

* The STAGING_IP_ADDRESS variable should be given the IP address of your staging 
server as its value. 

* Variables whose names begin with `staging_*` and have `0` values will be 
automatically updated with their proper values during Ansible provisioning of 
your staging server. 

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
STAGING_GIGADB_DB | gigadb
STAGING_GIGADB_HOST | dockerhost
STAGING_GIGADB_PASSWORD | vagrant
STAGING_GIGADB_USER | gigadb
STAGING_HOME_URL | somevalue
STAGING_IP_ADDRESS | somevalue
STAGING_PUBLIC_HTTPS_PORT | 433
STAGING_PUBLIC_HTTP_PORT | 80
staging_private_ip | 0
staging_public_ip | 0
staging_tlsauth_ca | 0
staging_tlsauth_cert | 0
staging_tlsauth_key | 0

> The value of the `STAGING_HOME_URL` variable is the domain name you will use for
the server on which you will deploy a staging or production GigaDB application.
The domain name can be created using your domain registration service.

### Executing a Continuous Integration run
 
Your CI/CD pipeline can now be executed:

* Go to your pipelines page and click on *Run Pipeline*.

* In the *Create for* text field, confirm the name of the branch you want to run 
the CI/CD pipeline. The default branch should already be pre-selected for you. 
Then click on the *Create pipeline* button. 

* Refresh the pipelines page, you should see the CI/CD pipeline running. If the 
set up of your pipeline is successful, you will see it run the build, test, 
security and conformance stages defined in the `gitlab-ci.yml` file.
 
## Continuous Deployment in the CI/CD pipeline

The deployment of `gigadb-website` code onto a staging or production server to 
provide a running GigaDB application is not automatically performed by the 
CI/CD pipeline since it is set to run manually in the `gitlab-ci.yml` file. 
This part of the CI/CD process has to be explicitly executed from the GitLab 
pipelines page.

Prior to this, a host machine has to be instantiated with a 
secure Docker daemon on which the GigaDB application will be deployed. This 
machine can be used for a specific environment, most likely staging or 
production. Two tools are used to deploy a Docker server on the AWS cloud: 
Terraform and Ansible.

### Terraform

[Terraform](https://www.terraform.io) is a tool which allows you to describe and
instantiate infrastructure as code.

Install Terraform-0.11 by downloading the installer from the 
[Terraform](https://www.terraform.io) web site or it can be installed using a 
package manager for your operating system. For example, MacOSX users can use 
[Macports](https://www.macports.org):
```
$ sudo port install terraform-0.11
```

Create the following environment variables with the required values which 
Terraform will use to access AWS:
```
$ export TF_VAR_deployment_target=staging
$ export TF_VAR_aws_vpc_id=<AWS VPC id>
$ export TF_VAR_aws_access_key=<AWS Access key>
$ export TF_VAR_aws_secret_key=<AWS Secret key>
```

>You could also add the above lines into your `~/.bash_profile` file to save 
having to repeatedly execute the `export` commands.

Terraform describes infrastructure as code in text files ending in *.tf*. There 
is such a file in`ops/infrastructure/aws-ec2.tf` and this is used to create a 
t2.micro instance on AWS with the security privileges that allow communication 
with a Docker daemon. An AWS resource is specified which you will log in with a 
key pair named `aws-centos7-keys`. Create this key pair using your AWS console
web page.

Download the private key file from AWS and place it in your `~/.ssh` directory 
so that its path will be `~/.ssh/aws-centos7-keys.pem`.

Create an elastic IP (EIP) address for your staging server hosting GigaDB
with the name `eip-staging-gigadb`. The `aws-ec2.tf` file will instruct 
Terraform to look for this EIP and automatically associate it with the EC2 
instance. If there is no EIP called `eip-staging-gigadb` then Terraform will 
generate a `no matching Elastic IP found` error message.

> Using your domain name service, map the EIP to the domain name you will use 
for your staging or production server.

In order to avoid accidental deletion of provisioned infrastructure, it is highly recommended to maintain distinct Terraform state for each target environment. This also make it easier using different and segregated cloud accounts for each environment. 

Thus the main Terraform configugration, state and variables will be kept in environment-specific directory.

Furthermore, that approach reduce code duplication as the common Terraform code can be kept in modules in a separate directory (``ops/infrastructure/modules``). From now on in the doc, we will take the example of the **staging** environment. When creating a new environment, one can just duplicate the ``ops/infrastructure/envs/staging`` directory and adjust values.

Use Terraform to instantiate the t2.micro instance on AWS cloud with the 
following commands:
```
$ cd ops/infrastructure/envs/staging
$ terraform init
$ terraform plan
$ terraform apply
```

*N.B.* Use `terraform destroy` to terminate the EC2 instance.

Check that your new EC2 instance exists on your AWS Web console. It will have 
the name `ec2-as1-staging-gigadb`.

Reconcile the Terraform state file with the actual AWS infrastructure to update 
public IP address of the staging_dockerhost instance with the elastic IP 
address:
```
$ terraform refresh
```

If this is not done then Ansible will try to use the initial IP address of 
your EC2 instance and you will get a server not found error since the server
will not be associated with your elastic IP address.

### Ansible

The Ansible software is a tool for provisioning, managing configuration and 
deploying applications using its own declarative language. SSH is used to 
connect to remote servers to perform its provisioning tasks.
[Ansible](https://www.ansible.com) is used to install the EC2 instance 

with a Docker daemon. In addition, a PostgreSQL server is installed on the EC2
instance which will host the database that GigaDB uses to manage information
abouts its datasets. Note that this setup for a staging instance of GigaDB is
different to a local GigaDB application whose PostgreSQL database is provided by
a custom Docker container.


#### Ansible setup and configuration

Like Terraform, we keep the Ansible configuration files in environment-specific directories, and we execute Ansible in these directories. The reasons are exactly the same as for Terraform (Safety, DRY, Cloud account flexibility).

The main concepts used in Ansible are Hosts, Roles, Tasks and Playbook.

The sets of software configuration instructions we want to perform on the infrastructure provisioned with Terraform in the previous step, are Tasks (e.g: Enable systemd service).

Tasks are grouped into Roles (e.g: docker-postinstall). 

The Playbook is the file describing the sequence of Roles (and/or Tasks) to be performed on a collection of Hosts whose software configuration we want to bring to a certain state.

Hosts can be defined statically, dynamically or a combination of both and from one or more sources.

Best practices is to use Ansible in agent-less way, so it needs connection parameters in order to control the remote provisioned machine.

The host name and IP address on which to run ansible are an output of running terraform, so we are going to feed ansible the host name and ip adress dynamically.

However the connection parameters (like SSH keys) are variables we need to supply statically and they are different for each environment.

In this paragraph and the next, from now on we will assume we are dealing with the **staging** environment.

The machines controlled by Ansible are usually defined in a [`hosts`](https://github.com/gigascience/gigadb-website/blob/develop/ops/infrastructure/envs/staging/group_vars/docker_host/vars)
file which lists the host machines connection details. Our file is located at `ops/infrastructure/envs/staging/group_vars/docker_host/vars` and contains
the following content:
```
ansible_ssh_private_key_file: "{{ vault_staging_private_key_file_location }}"
ansible_user: "centos"
ansible_become: "true"
database_bootstrap: "../../../../sql/production_like.pgdmp"
pg_user: "{{ vault_staging_pg_user }}"
pg_password: "{{ vault_staging_pg_password }}"
pg_database: "{{ vault_staging_pg_database }}"
gitlab_url: "{{ vault_gitlab_url }}"
gitlab_private_token: "{{ lookup('file','~/.gitlab_private_token') }}"
gigadb_environment: staging

fuw_db_user: "{{ vault_staging_fuw_db_user }}"
fuw_db_password: "{{ vault_staging_fuw_db_password }}"
fuw_db_database: "{{ vault_staging_fuw_db_database }}"
```

Our `vars` file does not list any machines. Instead, a tool called 
[`terraform-inventory`](https://github.com/adammck/terraform-inventory)  
generates a dynamic Ansible inventory from a Terraform state file. Nonetheless, 
the `vars` file is still used to reference variables for hosts.

* One particular variable to note is `gitlab_private_token`. The value of `gitlab_private_token`
is the contents of a file located at `~/.gitlab_private_token`.  Create this 
file using the [GitLab personal access token](https://docs.gitlab.com/ee/user/profile/personal_access_tokens.html)
that you will use to access the GitLab API. N.B. The `read_user` and 
`read_registry` scopes are not required when creating the private token.

The values of some of the variables in the `hosts` file are sensitive and for 
this reason, the actual values are encrypted within an Ansible vault file which 
needs to be located at `ops/infrastructure/envs/staging/group_vars/docker_host/vault`. This vault 
file should NOT be version controlled as defined in the `.gitignore` file.

Create the `vault` file in the ops/infrastructure/envs/staging/group_vars/docker_host directory:
```
$ pwd
~/gigadb-website
# Make a directory for group variables
$ mkdir ops/infrastructure/group_vars 
# Make a directory for all
$ mkdir ops/infrastructure/group_vars/all
# Create vault file
$ ansible-vault create ops/infrastructure/group_vars/all/vault
```

You will be prompted to enter a password, which you will need to share with 
others needing access to the vault. The variables below with appropriate values 
need to be placed in the `vault` file:
```
vault_staging_pg_user: somevalue
vault_staging_pg_password: somevalue
vault_staging_pg_database: somevalue
# Path to AWS pem file
vault_staging_private_key_file_location: somevalue

vault_production_pg_user: somevalue
vault_production_pg_password: somevalue
vault_production_pg_database: somevalue
vault_production_private_key_file_location: somevalue

vault_private_gitlab_token: somevalue
# Base URL of GitLab project
vault_gitlab_url: somevalue
```

An example of what the `vault_gitlab_url` should look like is:
```
vault_gitlab_url: "https://gitlab.com/api/v4/projects/gigascience%2Fforks%2Fjbloggs-gigadb-website"
```

Save the `vault` file when you are done. Since the `vault` file is encrypted, 
you will see something like this if you try to edit the file in a text editor:
```
$ANSIBLE_VAULT;1.2;AES256;dev
37636561366636643464376336303466613062633537323632306566653533383833366462366662
6565353063303065303831323539656138653863353230620a653638643639333133306331336365
62373737623337616130386137373461306535383538373162316263386165376131623631323434
3866363862363335620a376466656164383032633338306162326639643635663936623939666238
3161
```

To open the encrypted `vault` file for editing, use the command below and input
the password when prompted.
```
$ ansible-vault edit ops/infrastructure/envs/stagings/group_vars/docker_host/vault
```

Provide Ansible with the password to access the vault file during the 
execution of playbooks by storing the password to the vault file in a 
`~/.vault_pass.txt` file. 

Roles are used in Ansible to perform tasks on machines such as installing a  
software package. An Ansible role consists of a group of variables, tasks, files 
and handlers stored in a standardised file structure. There are a number of 
roles in `ops/infrastructure/roles` for installing Docker, PostgreSQL and 
security tools on hosts. Other roles are required which are available from 
public repositories.

Download these roles:
```
$ ansible-galaxy install -r requirements.yml
```

#### Ansible playbook execution

Provision the EC2 instance using Ansible:
```
$ cd ops/infrastructure/envs/staging
$ ansible-playbook -vvv -i docker_host -i /usr/local/bin/terraform-inventory  playbook.yml --vault-password-file ~/.vault_pass.txt
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

 
This is for running a secure Docker engine on a cloud virtual server so that the 
Docker API is secured over TCP and we know we are communicating with the correct 
server and not a malicious impersonation. We also need to authenticate the 
client with TLS so only clients using the client certificates can use the Docker 
engine. This is the 2-way certificate-based authentication.

### Further configuration steps

The new `gigadb-website` code contains functionality for running GigaDB over 
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

Deployment of GigaDB on the EC2 staging server in the CI/CD process is 
described in the [gitlab-ci.yml](https://github.com/gigascience/gigadb-website/blob/develop/.gitlab-ci.yml) 
file. The CI/CD process has a stage called `with_new_cert_deploy` which includes
a step to generate a web certificate for TLS termination on the web container 
for GigaDB. As mentioned above, the value of the $STAGING_HOME_URL CI/CD
environment variable in this file will be your domain name which ensures that 
the certificate is created for your domain name. $STAGING_HOME_URL is a GitLab 
CI/CD environment variable so its value must be provided on the GitLab.com web 
site.

#### NGINX configuration

In addition, NGINX conf files need to be configured with the domain name of the 
staging server. Edit the following two NGINX conf files so that any mention of 
`gigadb-staging.gigatools.net` is replaced with the domain name you are using to 
stage GigaDB.

* `ops/configuration/nginx-conf/sites/gigadb.staging.http.conf`
* `ops/configuration/nginx-conf/sites/gigadb.staging.https.conf`

Commit these two NGINX conf files into your repository. You should see a new 
CI/CD pipeline process start and end with a successful build. 

### Executing the CD pipeline for deployment

Actual deployment of GigaDB to the staging server is manually performed by 
clicking a button on the GitLab pipelines page for your GitLab GigaDB project. 
If it is the first time doing this on the server, select the 
*with_new_cert_deploy* process, otherwise, use *deploy_app*. 

Also, note that the HTTPS certificates last 3 months, so you need to do at least 
one deploy every 3 month (a NO-OP deploy will work).

## PostgreSQL database in GigaDB application

The virtual server hosting the Docker engine also hosts the PostgreSQL database
that GigaDB uses to store information about datasets, users, etc. If you want to 
access the database then it is possible to connect to it using 
[pgAdmin4](https://www.pgadmin.org). This is done by creating a new server on 
the pgAdmin interface and using SSH Tunneling to access the virtual server with 
the following parameters:
```
Tunnel host:        <Public IP address of virtual server>
Tunnel port:        22
Username:           centos
Authentication:     Identity file
Identity file:      <Path to AWS pem file>
```

For the Connection parameters:
```
Hostname:               127.0.0.1
Port:                   5432
Maintenance database:   gigadb
Username:               gigadb
```

Use `vagrant` as the password to access the database by the `gigadb` user.

There is also command line access to the PostgreSQL database on the virtual
server:
```
$ ssh -i "aws-centos7-keys.pem" centos@ec2-**-***-***-***.ap-southeast-1.compute.amazonaws.com
# Use vagrant as password
$ psql -U gigadb -h localhost
gigadb=>
```
