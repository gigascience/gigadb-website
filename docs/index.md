# GigaDB Documentation

This page contains the documentation for the GigaDB website. The documentation is written in Markdown and is built using [Material for MkDocs](https://squidfunk.github.io/mkdocs-material/getting-started/),
which is a well known third party theme for MkDocs, a static site generator geared towards project documentation.

## Table of contents

##### A. Overview and local Environment Setup
- [Setup Guide](sop/LOCAL_SETUP.md)

##### B. Production Environment Setup
- [AWS Setup](sop/AWS_SETUP.md)
- [Infrastructure Provisioning on AWS](sop/SETUP_PROVISIONING.md)
- [CI/CD Pipeline Setup](sop/SETUP_CI_CD_PIPELINE.md)

##### C. Developer SOPs
- [Dataset Upload](sop/DATASET_UPLOAD.md)
- [Deploy and Configure Public FTP Server](sop/DEPLOY_AND_CONFIGURE_PUBLIC_FTP_SERVER.md)
- [EFS Data Migration](sop/EFS_DATA_MIGRATION.md)
- [Migration Troubleshoot](sop/MIGRATION_TROUBLESHOOT.md)
- [May2024-temp-dataset-creation-workflow](sop/May2024-temp-dataset-creation-workflow.md)
- [Performances Troubleshoot](sop/PERFORMANCES_TROUBLESHOOT.md)
- [Production Deploy](sop/PRODUCTION_DEPLOY.md)
- [Restore Database AWS Backup](sop/RESTORE_DATABASE_AWS_BACKUP.md)
- [Restore Database AWS Snapshot](sop/RESTORE_DATABASE_AWS_SNAPSHOT.md)
- [Restore Database Backup in S3](sop/RESTORE_DATABASE_BACKUP_IN_S3.md)
- [Roll Back Prev Deploy](sop/ROLL_BACK_PREV_DEPLOY.md)
- [Update Version](sop/UPDATE_VERSION.md)
- [Wasabi Data Migration](sop/WASABI_DATA_MIGRATION.md)

##### D. Production Release
- [Releases How-To](sop/RELEASE_PROCESS.md)

##### E. Monitoring
- [Monitoring](sop/MONITORING.md)
- [Uptime Status Page](miscellaneous/UPTIME_STATUS_PAGE.md)

##### F. Troubleshooting
- [Performances Troubleshoot](sop/PERFORMANCES_TROUBLESHOOT.md)
- [Production Troubleshooting Guide](sop/PRODUCTION_TROUBLESHOOT.md)

##### G. Miscellaneous
- [System Architecture](miscellaneous/SystemArchitectureExport.png)
- [TLS](miscellaneous/TLS.md)
- [Security Guidelines](miscellaneous/SECURITY.md)
- [Variables](miscellaneous/variables.md)
- [FUW workflow](miscellaneous/file-upload-workflow-wireframes.pdf)



## Start and accessing the documentation server using Docker

```
$ cd gigadb-website/
$ docker run --rm -it -p 8000:8000 -v ${PWD}:/docs squidfunk/mkdocs-material
or
$ docker-compose up -d mkdocs
```

the documentation will be available at(http://localhost:8000/

## Building the documentation site

To create a documentation web pages:

```
$ mkdocs build
$ ls site
about  fonts  index.html  license  search.html
css    img    js          mkdocs   sitemap.xml
```

## Deploying the documentation site

## 

