# How to set up CI/CD pipelines on gitlab.com

Modern software application development may involve implementing small code 
changes which are frequently checked into version control. Continuous 
Integration (CI) provides a consistent and automated way to build, package and 
test the application under development. Furthermore, Continuous Delivery (CD) 
automates the deployment of applications to specific infrastructure environments 
such as staging and production servers.

## Use of GitLab for Continuous Integration

GitLab provides a CI service used by GigaDB based on the 
[`.gitlab-ci.yml`](https://github.com/gigascience/gigadb-website/blob/develop/.gitlab-ci.yml)
file located at the root of the repository. A Runner in GitLab is configured to 
trigger the CI pipeline every time there is a code commit or push. GitLab.com
allows you to use Shared Runners provided by GitLab Inc which are virtual 
machines running on GitLab's infrastructure to build any project.

The GigaDB `.gitlab-ci.yml` file tells the GitLab Runner to run a pipeline job 
with these stages: build, test, security, conformance, staging and live. The 
status of every pipeline is displayed in the Pipelines page.

### Mirroring your forked gigadb-website repository from GitHub

To begin, we need to mirror your forked GitHub gigadb-website repository in a 
GitLab project. This is done by adding your GitHub gigadb-website repository to 
the GitLab Gigascience Forks organisation. To do this:

* Log into GitLab and go to the 
[gigascience/Forks page](https://gitlab.com/gigascience/forks).
 
* Click on *New Project* followed by *CI/CD for external repo* and then 
*GitHub*. 

* On the *Connect repositories from GitHub page*, click on the 
*List your GitHub repositories* green button. Find the repository fork of 
`gigadb-website` that you want to perform CI/CD on.

* Under the *To GitLab* column, select *gigascience/forks* to connect your repo 
to this GitLab group. Also, provide a name for the repo, *e.g.* 
pli888-gigadb-website so that you can differentiate this repository from others 
in the Forks group.

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
section in the CI/CD Settings page. Any values below listed as **?** should be 
replaced with proper values - please contact the GigaScience tech support team 
for these. These environment variables together with those in the Forks group 
are exported to the `.secrets` file and are listed 
[here](https://github.com/gigascience/gigadb-website/blob/develop/ops/configuration/variables/secrets-sample).
                             

Variable Name | Value
------------- | -----
ANALYTICS_CLIENT_EMAIL | **?**
ANALYTICS_CLIENT_ID | **?**
ANALYTICS_PRIVATE_KEY | **?**
COVERALLS_REPO_TOKEN | **?**
FORK | **?**
MAILCHIMP_API_KEY | **?**
MAILCHIMP_LIST_ID | **?**
MAILCHIMP_TEST_EMAIL | **?**
STAGING_GIGADB_DB | gigadb
STAGING_GIGADB_HOST | dockerhost
STAGING_GIGADB_PASSWORD | vagrant
STAGING_GIGADB_USER | gigadb
STAGING_HOME_URL | 0
STAGING_IP_ADDRESS | 0
STAGING_PUBLIC_HTTPS_PORT | 433
STAGING_PUBLIC_HTTP_PORT | 80
staging_private_ip | 0
staging_public_ip | 0
staging_tlsauth_ca | 0
staging_tlsauth_cert | 0
staging_tlsauth_key | 0

### Executing a Continuous Integration run
 
Your CI/CD pipeline can now be executed:

* Go to your pipelines page and click on *Run Pipeline*.

* In the *Create for* text field, confirm the name of the branch you want to run 
the CI/CD pipeline. The default branch should already be pre-selected for you. 
Then click on the *Create pipeline* button. 

* Refresh the pipelines page, you should see the CI/CD pipeline running. If the 
set up of your pipeline is successful, you will see it run the build, test, 
security and conformance stages.

### Triggering CI runs with new code commits

Since our repository includes a .gitlab-ci.yml file, any new commits will 
trigger a new CI run. To see an example of this happening, edit the 
`~/.gitlab-ci.yml` file by changing two variables. Firstly, change 
`GITLAB_UPSTREAM_PROJECT_ID` is updated to its correct value which you can find 
in the `General project` section in Settings page for your GitLab project. 
Secondly, edit `MAIN_BRANCH` so that its value is set to the repository branch
under CI/CD pipeline processing.
```
MAIN_BRANCH: "remove-chef-vagrant"
GITLAB_UPSTREAM_PROJECT_ID: "10678502"
```  
 
Commit and push these changes to your GitHub repository. If you now go to the 
CI/CD > Pipelines page, you should see a new pipeline with a `running` status 
which was triggered by the above code commit which is mirrored in the GitLab
project repository.
 
## Continuous Deployment in the CI/CD pipeline

The deployment of gigadb-website code onto a staging or production server to 
provide a running GigaDB application is not automatically performed by the 
CI/CD pipeline since it is set to run manually in the `.gitlab-ci.yml` file. 
Therefore, this part of the CI/CD process has to be explicitly executed from the 
[GitLab pipelines](https://gitlab.com/gigascience/forks/pli888-gigadb-website/pipelines)
page. Prior to this, a server has to be instantiated with an installation of the
Docker daemon to manage containers and images, and this can be done using 
Terraform and Ansible to create a Docker server on AWS.

### Terraform

[Terraform](https://www.terraform.io) is a tool which allows you to describe and
instantiate infrastructure as code. Terraform can be installed by downloading
the installer from the [Terraform](https://www.terraform.io) web site or it can 
be installed using a package manager for your operating system. For example, 
MacOSX users can use [Macports](https://www.macports.org).

The following environment variables with the required values need to be created 
which Terraform will use to access AWS:
```
$ cd ops/infrastructure
$ export TF_VAR_deployment_target=staging
$ export TF_VAR_aws_vpc_id=<AWS VPC id>
$ export TF_VAR_aws_access_key=<AWS Access key>
$ export TF_VAR_aws_secret_key=<AWS Secret key>
```

You could also persist the above variables in your `~/.bash_profile` file.

Terraform describes infrastructure as code in text files ending in *.tf*. There 
is a such a file in`ops/infrastructure/aws-ec2.tf` and this is used to create a 
t2.micro instance on AWS with the security privileges that allow communication 
with a Docker daemon. Note that this `tf` file specifies an AWS resource which 
you will log in with a key pair named `aws-centos7-keys` so this needs to be 
created. The private key file which you will have downloaded from AWS should be 
placed in your `~/.ssh` directory so its path will be 
`~/.ssh/aws-centos7-keys.pem`.

* Create an elastic IP (EIP) address for your staging server hosting GigaDB
with the name `eip-staging-gigadb`. The `aws-ec2.tf` file will instruct 
Terraform to look for this EIP and associate it with the EC2 instance. If an EIP
called `eip-staging-gigadb` does not exist then Terraform will generate a
`no matching Elastic IP found` error message.

* Use Terraform to instantiate the t2.micro instance on AWS cloud:
```
$ terraform init
$ terraform plan
$ terraform apply
```

*N.B.* Use `terraform destroy` to terminate the EC2 instance.

Check that your new EC2 instance exists using your AWS Web console.

Reconcile terraform state file with actual AWS infrastructure to update public 
IP address of the staging_dockerhost instance with elastic IP address 
otherwise Ansible will try to use the original EC2 instance IP address and you
will get a server not found error:
```
$ terraform refresh
```

### Ansible

[Ansible](https://www.ansible.com) is now used to install the EC2 instance 
with a Docker daemon. The Ansible software is a tool for provisioning, managing
configuration and deploying applications using its own declarative language. SSH
is used to connect to remote servers to perform its provisioning tasks.

### Ansible setup and configuration

The machines controlled by Ansible are defined in a `hosts` file which lists 
host groups and the hosts within these groups. The `hosts` file for the 
gigadb-website project is at `ops/infrastructure/inventories/hosts`.

* Check this file so that the `ansible_ssh_private_key_file` variable contains 
the correct path to your AWS pem file.
* If not present, create a [`~/.gitlab_private_token`](https://docs.gitlab.com/ee/user/profile/personal_access_tokens.html) 
file since this is referenced in the `hosts` file and provides access to the 
GitLab API. The `read_user` and `read_registry` scopes are not required when
creating the private token.

Roles are used in Ansible to perform tasks such as installing a piece of 
software. An Ansible role consists of a group of variables, tasks, files and 
handlers stored in a standardised file structure. There are a number of roles in
`ops/infrastructure/roles` for installing Docker, PostgreSQL and security 
tools on hosts.

Other roles are required which are available from public repositories and these
should be downloaded as follows:
```
$ ansible-galaxy install -r requirements.yml
```

Database security credentials are placed in an encrypted file which is created 
using Ansible Vault. Place the vault file in 
`ops/infrastructure/group_vars/all/vault`. This vault file should NOT be version 
controlled as defined in the `.gitignore` file.

Opening the encrypted file for editing to adjust the database credentials can be 
done using the command below. N.B. you will be prompted for a password.
```
$ ansible-vault edit ops/infrastructure/group_vars/all/vault
``` 

Provide Ansible with the password so it can access the vault file during the 
execution of playbooks by storing the password in a `~/.vault_pass.txt` file. 

>The docker-postinstall role needs to be updated because the name of the GitLab
project whose environmental variables are edited by this role is hard-coded into
the `ops/infrastructure/roles/docker-postinstall/tasks/main.yml` file so the 
specific name of your GitLab project needs to be edited accordingly here.

> A possible fix for this is to do env variable lookups from the .env file which 
contains the gitlab project url we need in the main.yml file.
 

### Ansible playbook execution

To provision the EC2 instance using Ansible:
```
$ ansible-playbook -vvv -i inventories staging-playbook.yml --vault-password-file ~/.vault_pass.txt
```

> Since an elastic IP address is being used, you might need to delete the entry
in the `~/.ssh/known_hosts` file associated with the elastic IP address.

Ansible will update values for specific project environment variables in 
GitLab. Check them on the project environment variables page after the Ansible
provisioning has completed. This is done by the docker-postinstall role. N.B.
make sure that the correct gitlab project url is being used here in this 
`main.yml` file.

* staging_tlsauth_ca - certificate authority for staging server - this is 
provided by staging server during Ansible provisioning
* staging_tlsauth_cert - public certificate for staging server - this is 
provided by staging server during Ansible provisioning
* staging_tlsauth_key - the server key for the above CA - this is provided by 
staging server during Ansible provisioning
 
This is for running a secure Docker engine on the production CNGB virtual server
so that the Docker API is secured over TCP and we know we are communicating 
with the correct server and not a malicious impersonation. We also need to 
authenticate the client with TLS so only clients using the client certificates 
can use the Docker engine. This is the 2-way certificate-based authentication.

### Further configuration steps

The new gigadb-website code contains functionality for running GigaDB over 
[HTTPS](https://en.wikipedia.org/wiki/HTTPS). The 
[Let's Encrypt](https://letsencrypt.org) certificate authority is used as a 
trusted authority to sign a certificate provided by GigaDB which is trusted by 
users.

* For Let's Encrypt to do this, the server used for deployment requires a domain 
name. The EC2 domain names provided by AWS cannot be used because they are 
ephemeral and so are blacklisted by Let's Encrypt and your own domain name must 
be used instead, *e.g.* [http://gigadb-staging.gigatools.net]. Let's Encrypt 
verifies that this domain name is under our control.

* The .gitlab-ci.yml file needs to be edited to use your domain name by changing
`gigadb-staging.pommetab.com` to, for example, `gigadb-staging.gigatools.net`, 
the domain name for your server where GigaDB will be located.

> Actually, all mention of `gigadb-staging.pommetab.com` in files need to be 
changed to, for example, `gigadb-staging.gigatools.net`. Check out:
* ops/configuration/nginx-conf/sites/gigadb.staging.http.conf
* ops/configuration/nginx-conf/sites/gigadb.staging.https.conf

### Executing the CD pipeline for deployment

Deployment to the staging server is manually performed by clicking a button on 
the GitLab Pipeline page. If it is the first time doing this on the server, 
select the *with_new_cert_deploy* process. Otherwise, use *deploy_app*. 

Also note that the HTTPS certificates last 3 months, so you need to do at least 
one deploy every 3 month (a NO-OP deploy will work).