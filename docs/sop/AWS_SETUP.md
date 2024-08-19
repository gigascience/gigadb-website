# AWS SETUP for the Upstream projects

This document is a SOP is for preparing the infrastructure used for the live production environments of GigaDB. This document is for core team members who need to work on live production infrastructure.

## Pipeline and provisioning

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

| project | AWS profile | AWS region | current role | current release | status |
| -- | -- | -- | -- | -- | -- |
| gigadb-website | Upstream | ap-east-1 | current production | v4.3.3 | OK |
| alt-gigadb-website | UpstreamAlt | ap-southeast-2 | hot stand-by | v4.3.3 | OK |

>**Note**: this table needs updating after each deployment or incident failover



[1] https://en.wikipedia.org/wiki/Hot_spare

[2] https://en.wikipedia.org/wiki/Blue–green_deployment

[3] https://aws.amazon.com/blogs/architecture/creating-an-organizational-multi-region-failover-strategy/

## Configuring AWS for Upstream projects

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

## Preparation of the hot stand-by

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

The table below shows the mappings between the fully qualified domain name (FQDN) and the AWS EIPs.
You will need to look up the actual IP in the AWS EC2 dashboard.
You can look up the corresponding Gitlab project and AWS region  in the table at the top of this document.
The DNS records are to be saved in the Cloudflare dashboard.

>**Note**: the EIP names are the same across both infrastructure

#### Current production

| FQDN | EIP |  environment |
| --- | --- | --- |
| gigadb.org | eip-gigadb-live-gigadb  |  live |
| bastion.gigadb.org | eip-gigadb-bastion-live-gigadb |  live |
| files.gigadb.org | eip-gigadb-files-live-gigadb |  live |
| portainer.gigadb.org |  eip-gigadb-live-gigad |  live |
| staging.gigadb.org | eip-gigadb-staging-gigadb  |  staging |
| bastion-stg.gigadb.host | eip-gigadb-bastion-staging-gigadb |  staging |
| files.staging.gigadb.org | eip-gigadb-files-staging-gigadb |  staging |
| portainer.staging.gigadb.org |  eip-gigadb-staging-gigadb |  staging |


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

You can now follow the process described in `docs/SETUP_CI_CD_PIPELINE.md` and `docs/SETUP_PROVISIONING.md` while remembering that:
* for terraform, prefix the command with `AWS_PROFILE=Upstream` or `AWS_PROFILE=UpstreamAlt` depending on which role you are deploying
* for Ansible, make sure to configure one of the AWS regions in the table at the top depending on which role you are deploying

## Keeping data in sync

The relevant data stores are:
* EFS for the user dropboxes
* RDS for the files metadata

Regularly, the data from both stores on the current production needs to be synchronised to the hot stand-by.

| Data store | method | Frequency/time | what infra performs the synching | script        |
| --- | --- | --- | --- |---------------|
| EFS | rlcone sync | daily | hot stand-by | s3backup      |
| RDS | pgsql load from RDS backup | daily | hot stand-by | databaseReset |

>**Note**: `s3backup` and `databaseReset` needs to understand backup keyed on date (YYYYMMDD) and date time (YYYYMMDDHHMM). The former is used for cron driven daily backup and restore, while the latter is for manual backup and restore, during blue/green deployment and other ad-hoc situations.


## Deploying releases


Both Gitlab projects track gigadb-website repo on GitHub and will deploy to their respective staging production environments automatically.

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


Steps to perform:
* Deploy to hot stand-by the release
* Setup a change embargo on current production
* Turn off the data stores sync on the hot stand by
* Manually run S3backup with date+time suffix
* On the hot standby, manually run `databaseReset <date+time>`
* Have the business team validate the hot standby deployment (Blue)
* Swap the two DNS records between the EIPs of the current production and the hot stand by
* Blue is now live, make announcement and perform sanity check

Two possibilities from there:
* Release on Blue is OK, then deploy the release to the former current-production, now hot stand-by (Green), and turn on both data stores sync cronjob on Green
* Release on Blue is KO and the business want reverting, then perform the opposite DNS records swap then investigate on hot stand-by

Steps to always perform:
* Ensure the table at the top of this document is always correctly representing reality
