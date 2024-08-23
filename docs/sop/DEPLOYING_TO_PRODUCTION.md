# Deploying to production for the Upstream projects

This document is a SOP is for preparing the infrastructure used for the live production environments of GigaDB. 
This document is for core team members who need to work on live production infrastructure.

The base principles, tooling, configuration and provisioning approach are the same that used for developers environments on AWS.
The implications are:
* You must be familiar with the content of and have provisioned and deployed following the guidance of `docs/SETUP_CI_CD_PIPELINES.md` and `docs/SETUP_PROVISIONING.md`
* This doc will focus only on the specificities of the Upstream projects, as you can refer to the above docs for everything else

## Upstream projects

There are two Upstream projects on Gitlab, `gigadb-website` and `alt-gigadb-website`.
Together they represent an implementation of:
* An high availability strategy called "Hot Stand-By"  [1]
* A deployment strategy called "Blue/Green" [2]
* A failover strategy, AWS calls "Portfolio failover" [3]

The two infrastructures are meant to be identical and independent, and must run on two distinct regions. Each infrastructure has a role and they will swap roles upon blue/green deployment or incident failover.

| Gitlab project     | AWS profile | Local checkout name | AWS region              | current role | current release | status |
|--------------------| --- |---------------------|-------------------------| --- |-----------------|--------|
| gigadb-website     | Upstream | gigadb-upstream     | ap-east-1 (HK)          | current production             | v4.3.4          | OK     |
| alt-gigadb-website | UpstreamAlt | gigadb-alt-upstream | ap-southeast-2 (Sydney) | hot stand-by   | v0337-alt-upstream-beta          | OK     |

>**Note**: this table needs updating after each deployment or incident failover



[1] https://en.wikipedia.org/wiki/Hot_spare

[2] https://en.wikipedia.org/wiki/Blue–green_deployment

[3] https://aws.amazon.com/blogs/architecture/creating-an-organizational-multi-region-failover-strategy/

## Configuring locally AWS for Upstream projects

Normally, in `~/.aws/credentials`, you should already have a profile called `[GigaDB]` that you have used for creating developer environments on AWS.
You now need two new profiles:
* `[Upstream]`: for the `gigadb-website` project
* `[UpstreamAlt]`: for the `alt-gigadb-website` project

You populate these sections with AWS access keys you create in the IAM dashboard for the GigaDB user.

```
[Upstream]
aws_access_key_id=<aws_access_key_id1 for Gigadb user>
aws_secret_access_key=<aws_secret_access_key1 for Gigadb user>

[UpstreamAlt]
aws_access_key_id=<aws_access_key_id2 for Gigadb user>
aws_secret_access_key=<aws_secret_access_key2 for Gigadb user>
```

>**Note**: D	o not use the same keys for both sections, as if that key led to the compromise of one infrastructure, then the other one is done for too. For similar reason, do not use the keys you are using on your AWS developers deployment.

Then, still in the IAM dashboard, you will need to create two pairs of SSH public keys for each infrastructure. Try make the name easily identifiable as you will be using both keys often. E.g:

* gigadb-upstream-alt-sydney-peter
* gigadb-upstream-hk-ken

Finally, you can update the `~/.aws/config` with two new sections:

```
[profile Upstream]
region=<see the table above>
output=json

[profile UpstreamAlt]
region=<see the table above>
output=json
```
## Configuring and using your local checkout to work with Upstream projects

### Configuration

You need a checkout of gigadb-website repository to work with each Upstream project.
To avoid errors, they should be  separate checkouts from the one you use for development.

To that effect, clone a new local copy of the website using the SSH endpoint (not the HTTP),
so that, as a core member, you can pull and push to the remote repository

```
git clone git@github.com:gigascience/gigadb-website.git gigadb-upstream
```

Verify that you are tracking the main repository using SSH:
```
cd gigadb-upstream
```
```
git remote -v
```
Should return:
```
origin	git@github.com:gigascience/gigadb-website.git (fetch)
origin	git@github.com:gigascience/gigadb-website.git (push)
```

Next we need to set local environment variables to be used for this project.
```
cp ops/configuration/variables/env-sample .env
```
then set the following variables to these values:
```
REPO_NAME="gigadb-website"
GROUP_VARIABLES_URL="https://gitlab.com/api/v4/groups/gigascience/variables?per_page=100"
FORK_VARIABLES_URL="https://gitlab.com/api/v4/groups/3506500/variables"
PROJECT_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fupstream%2F$REPO_NAME/variables"
MISC_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fcnhk-infra/variables"
```

Don't forget to provide a value to the `GITLAB_PRIVATE_TOKEN` variable in that file.

Then repeat the above for the UpstreamAlt project:

```
git clone git@github.com:gigascience/gigadb-website.git gigadb-alt-upstream
```

Verify that you are tracking the main repository using SSH:
```
cd gigadb-alt-upstream
```
```
git remote -v
```
Should return:
```
origin	git@github.com:gigascience/gigadb-website.git (fetch)
origin	git@github.com:gigascience/gigadb-website.git (push)
```

Next we need to set local environment variables to be used for this project.
```
cp ops/configuration/variables/env-sample .env
```
then set the following variables to these values:
```
REPO_NAME="alt-gigadb-website"
GROUP_VARIABLES_URL="https://gitlab.com/api/v4/groups/gigascience/variables?per_page=100"
FORK_VARIABLES_URL="https://gitlab.com/api/v4/groups/3506500/variables"
PROJECT_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fupstream%2F$REPO_NAME/variables"
MISC_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fcnhk-infra/variables"
```

Don't forget to provide a value to the `GITLAB_PRIVATE_TOKEN` variable in that file.



### Usage

The activities to perform in those checkouts are:
* Create releases. See [docs/sop/RELEASE_PROCESS.md](docs/sop/RELEASE_PROCESS.md)
* Provision an environment for Upstream projects. See  [docs/sop/PROVISIONING_PRODUCTION.md](docs/sop/PROVISIONING_PRODUCTION.md)


## Configuring both Upstream projects in Gitlab and Cloudflare

The configuration of the infrastructures relies on Gitlab variables and DNS records.

### Gitlab variables

Make sure you have consulted the relevant sections of `docs/SETUP_CI_CD_PIPELINE.md` to know which variables need to be setup.
The below table show only the manually-set variables for which the  current production project  and the hot stand-by project have differing values

| Variable name |  environment | current production value | hot stand-by value |
| --- | --- | --- | --- |
| HOME_URL  | live | https://gigadb.org | http://alt-live.gigadb.host |
| REMOTE_HOME_URL | live | https://gigadb.org | http://alt-live.gigadb.host |
| REMOTE_HOSTNAME | live | gigadb.org | alt-live.gigadb.host |
| remote_fileserver_hostname | live | files.gigadb.org | files.alt-live.gigadb.host |
| HOME_URL  | staging | https://staging.gigadb.org | http://alt-staging.gigadb.host |
| REMOTE_HOME_URL |  staging | https://staging.gigadb.org | http://alt-staging.gigadb.host |
| REMOTE_HOSTNAME | staging | staging.gigadb.org | alt-staging.gigadb.host |
| remote_fileserver_hostname | staging | files.staging.gigadb.org | files.alt-staging.gigadb.host |


### DNS Records

Before you can configure the DNS records in Cloudflare, you need to have provisioned the production environments
as instructed in `docs/sop/PROVISIONING_PRODUCTION.md`.
The table below shows the mappings between the fully qualified domain name (FQDN) and the AWS EIPs.
You will need to look up the actual IP addresses corresponding to the EIP name in the AWS EC2 dashboard.
You can look up the corresponding Gitlab project and AWS region in the table at the top of this document.
The DNS records are to be saved in the Cloudflare dashboard.

>**Note**: the EIP names are the same across both infrastructure
>**Note**: TODO: We should create a command to perform DNS records swap so that we don't have to do it manually.

#### Current production

| FQDN | EIP                               |  environment |
| --- |-----------------------------------| --- |
| gigadb.org | eip-gigadb-live-gigadb            |  live |
| bastion.gigadb.org | eip-gigadb-bastion-live-gigadb    |  live |
| files.gigadb.org | eip-gigadb-files-live-gigadb      |  live |
| portainer.gigadb.org | eip-gigadb-live-gigadb            |  live |
| staging.gigadb.org | eip-gigadb-staging-gigadb         |  staging |
| bastion-stg.gigadb.host | eip-gigadb-bastion-staging-gigadb |  staging |
| files.staging.gigadb.org | eip-gigadb-files-staging-gigadb   |  staging |
| portainer.staging.gigadb.org | eip-gigadb-staging-gigadb         |  staging |


#### Hot Stand-by

| FQDN | EIP |  environment |
| --- | --- | --- |
| alt-live.gigadb.host | eip-gigadb-live-gigadb  |  live |
| bastion.alt-live.gigadb.host | eip-gigadb-bastion-live-gigadb |  live |
| files.alt-live.gigadb.org | eip-gigadb-files-live-gigadb |  live |
| portainer.alt-live.gigadb.host |  eip-gigadb-live-gigadb |  live |
| alt-staging.gigadb.host | eip-gigadb-staging-gigadb  |  staging |
| bastion.alt-staging.gigadb.host | eip-gigadb-bastion-staging-gigadb |  staging |
| files.alt-staging.gigadb.host | eip-gigadb-files-staging-gigadb |  staging |
| portainer.alt-staging.gigadb.host |  eip-gigadb-staging-gigadb |  staging |

### Pipeline and provisioning

You can now follow the process described in `docs/SETUP_CI_CD_PIPELINE.md` and `docs/SETUP_PROVISIONING.md` 
while remembering that:
* for terraform, prefix the command with `AWS_PROFILE=Upstream` or `AWS_PROFILE=UpstreamAlt` depending on which role you are deploying
* for Ansible, make sure to configure one of the AWS regions in the table at the top depending on which role you are deploying

## Keeping data in sync

The relevant data stores are:
* EFS for the user dropboxes
* RDS for the files metadata

Regularly, the data from both stores on the current production needs to be synchronised to the hot stand-by.

| Data store | method | Frequency/time | what infra performs the synching                     | script                                                            |
| --- | --- | --- |------------------------------------------------------|-------------------------------------------------------------------|
| EFS | rlcone sync | daily | hot stand-by                                         | sync_drobox                                                       |
| RDS | pgsql load from RDS backup | daily | current production (backup) + hot stand-by (restore) | s3backup (on current production), databaseReset (on hot stand-by) |

>**Note**: TODO: `s3backup` and `databaseReset` needs to understand backup keyed on date (YYYYMMDD) and date time (YYYYMMDDHHMM). 
> The former is used for cron driven daily backup and restore, while the latter is for manual backup and restore, during blue/green deployment and other ad-hoc situations.


## Deploying releases


Both Gitlab projects track `gigasciencve/gigadb-website` repo on GitHub 
and will deploy to their respective staging production environments automatically.

In both projects, deploying to the live production environment has to be triggered manually.
**But they should not be done at the same time**.

See below for the blue/green staggered approach.


### Blue/green deployment of the release


Basic principles:

* Always deploy to the live production environment on the Hot stand-by infrastructure first
* Always have the business teams (curators) and the tech team to manually validate the deployment to the Hot stand-by infrastructure
* When validation has come, change the DNS record to switch over the values for hot stand-by and current production.
* Only then, deploy the release to the new hot stand-by (formerly current production)
* From release to release (and if they are successful), `gigadb-website` and `alt-gigadb-website` will alternate roles

>**Note**:  Do not use The Gitlab names or the AWS profile to figure out which one is current production and which one is hot stand by as these name can refers to either. Use DNS mappings and the table at the top of the document

#### Process for complex changes

If the release contains infrastructure changes, and/or changes to the database schema, 
and/or brand new application deployment (e.g: FUW, Gigareview, ...)

Logical Steps to perform:
1. Setup a change embargo on current production infrastructure
2. Turn off the data stores sync on the hot stand by side
3. Manually run S3backup with date+time suffix
4. On the hot standby, manually run `databaseReset <date+time>`
5. Deploy the release to hot stand-by
6. Have the business team to validate the hot standby deployment (Blue)
7. If validated, Swap the DNS records between the EIPs of the current production and the hot stand by
   1. if not validated, see B.2 item below
8. Blue is now live, make announcement and perform sanity check

Two possibilities from there:

A) Release on Blue is OK
1. Turn on the RDS S3 backup cronjob on Blue
2. Deploy the release to the former current-production, now the new hot stand-by (Green)
3. Turn on EFS sync cronjob on Green
4. Turn on databaseReset cronjob on Green

B) Release on Blue has failed and the business want reverting,
1. Perform the opposite DNS records swap, that will immediately restore current production as before deployment
2. Investigate issue on Green

Steps to always perform whatever outcome:
* Lift change embargo on current production infrastructure
* Ensure the table at the top of this document always correctly represent reality

#### Process for simple changes

In this scenario, a release doesn't require infrastructure changes, nor database schema changes, 
and the changes are small and stateless.
In this case, there is no need to perform blue/green approach, and the process is almost similar to what we do now.
The only difference, is deployment needs to be done on both current production and hot stand by (starting with the latter) 
to keep them in sync. Also, it is necessary to update the table at the top of this document with the new release versions.
