# GigaDB Changelog

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Unreleased

- Feat #1334: add guest mode for freelancers to spin up local gigadb website without GitLab account
- Fix #1310 and #1311: Copy readme files created by readme tool into the 
  gigadb-datasets wasabi bucket.
- Feat #1294: Added tool into files metadata console to update dataset FTP site
  and file location URLs.
- Fix #1277: Fix terraform errors and warnings and update SOPS for database backup restoration
- Fix #1088: Excel spreadsheet upload will not insert non exist sample attribute in the spreadsheet into the database.
- Fix #1338: Enable persistent IP address using EIP for bastion hosts on AWS deployments
- Feat #580: Provision monitoring infrastructure with Prometheus and Grafana
- Feat #1282: Upgraded yii1.1 version to `1.1.28`, yii2 version to `2.0.48.1`, postgreSQL engine version to `14.8`, postgreSQL client version to `14`
