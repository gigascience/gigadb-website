# GigaDB Changelog

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Unreleased

- Feat #1371: Essential accessibility fixes on dataset page
- Fix #1561: show app version in footer on AWS deployment
- Feat #1420: Save bastion user's public key to gitlab variable page
- Feat #1456: Fix footer positioning to viewport bottom
- Feat #1423: Create bash script to update dataset and file links to Wasabi URLs

## v4.0.2 - 2023-11-27 - cba0ca7a

- Fix #1528: Do not use caching on mockup pages

## v4.0.1 - 2023-11-20 - 57129850

- Fix #1562: Make live tideways jobs only available in the tagged GitLab pipeline
- Feat #1365: Improve accessibility of header

## v4.0.0 - 2023-11-06 - 9c012f7b

- Fix #1416: Enable Gitlab tag pipeline and document release process
- Feat #1362: Add "skip to main" link
- Fix #1444: Make tideways build and deploy automatically for the production staging gigadb website
- Docs #1421: Update production deployment SOP, investigate terraform state synchronisation issues and create troubleshooting guide for the deployment process
- Feat #1460: Admin dashboard updated to same more modern look as the public side
- Fix #1102: On sample admin form, save all valid sample attributes while showing errors for non-existent attributes
- Feat #1434: Setup playwright local environment for automated accessibility testing
- Feat #1443: Make ansible playbooks can execute plays separately by adding tags
- Fix #1428: Increase resilience of provisioning by extracting saving EC2 IP addresses as standalone bootstrap plays
- Feat #1368: Improve accessibility of About Page
- Fix #1483: Fix URL creation for ftp_site field in dataset table when using files metadata console tool
- Feat #1374: Improve accessibility and use of semantic html of search results card
- Fix #1449: Fix issue preventing deployment to live production environment bastion server
- Fix #1102: Display error message when creating a sample object or updating an existing sample object with attribute not found in attribute table, and do not create/save it. Refactored container scanning jobs in gitlab pipeline.
- Feat #1376: Fix heading hierarchy in Contact Page, wrap address in `<address>` element
- Feat #1361: Add general accessible landmarks to layouts
- Feat #1363: Fix color contrast issues in pages with new layouts
- Feat #1309: Implement batch processing functionality for readme tool
- Feat #1356: Delete local bootstrap files and replace them with Bootstrap 2.0.4 from CDN
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

## v3.1.0 - 2017-01-18 - 83f9cf8

- for historical record

## v3.0.0 - 2016-05-12 - 5d89e0f

- for historical record
