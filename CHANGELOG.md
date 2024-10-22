# GigaDB Changelog

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).



## Unreleased

- Feat #1768: Alphabetically sorted dataset author dropdown options in adminDatasetAuthor form
- Fix #1843: Add top margin to table footer in dataset page
- Feat #2034: Maximize input text color contrast
- Feat #1993: Update contact address

## v4.3.8 - 2024-10-10 - 8f6f0d074 - live since 2024-10-22

- Feat #1903: Allow user to upload dataset files to wasabi bucket and also s3 glacier bucket for backup
- Feat #1771: Automatically mount EFS access point to bastion and webapp servers
- Fix #1861: Remove user suffix from wasabi profile and improve curators docs
- Feat #1893: Move new attribute inputs to the end of the adminFile form
- Fix #2048: Upgrade database Dockerfiles from buster to bullseye to fix failed pipeline jobs
- Feat security#2: Upgrade Bootstrap from v3.3.7 to v3.4.1

## v4.3.7 - 2024-09-24 - 7709c8545 - live since 2024-09-26

- Fix #2015: Error when deleting file on admin dasboard
- Fix #2029: Add new file attribute even if edit attribute fields are visible

## v4.3.6 - 2024-09-13 - 2d935c496 -

- Feat #1858: Relabel button that saves attribute in adminFile to avoid ambiguity
- Feat #1849: Able to toggle expand dataset sample attributes field

## v4.3.5 - 2024-08-23 - 4eaa1cda5 - live since 2024-08-29

- Feat #1853: Add tooltips to adminDataset, adminFile and datasetFunder forms
- Fix #1959: Relation cannot be blank error in Dataset:Relation admin form

## v4.3.4 - 2024-08-13 - fd9ed2480 - live since 2024-08-14

- Fix #1908: Correct and improve CI/CD setup documentation
- Fix #1856: Fix overlapping caret and table header in dataset page
- Security #dependabot/114: Command injection in fs-path
- Security #dependabot/115: Remote code execution in handlebars when compiling templates
- Security #dependabot/116: Prototype Pollution in handlebars
- Security #dependabot/172: Unsafe Reflection in base Component class in yiisoft/yii2

## v4.3.3 - 2024-08-05 - 7204ee854 - live since 2024-08-07

- Fix #1848: Save modifications to image metafields
- Feat #1872: Extract stages of postUpload script into separate scripts

## v4.3.2 - 2024-07-22 - 6531115b7 - live since 2024-07-25

- Feat #1892: Remove unused CSS and LESS files
- Fix #1871: Allowed to save species info filling only required inputs

## v4.3.1 - 2024-07-10 - 2ba5e7dbc - live since 2024-07-15

- Feat #1892: Consolidate all layouts into one single layout
- Fix #1912: Enable curators to save Gigadb forms from many browser tabs at once
- Feat #1840: Make create readme tool available as part of postUpload script

## v4.3.0 - 2024-06-25 - 9cf91f224 - live since 2024-07-01

- Feat #1892: Remove old layouts
- Fix #1845: Use file path to update md5 values and file sizes in database
- Fix #1812: Navigating tables on mockup pages does not generate errors
- Fix #1801: Refresh materialized views daily using cron job and drop existing triggers
- Feat #1143: Open external links in new browser tabs
- Feat: updated upload spreadsheet template to version 19
- Fix #1743: Accessibility tweaks on datasetSubmission/upload page

## v4.2.9 - 2024-06-06 - 30a64ce56 - live since 2024-06-11

- Feat #1869: Script to compare files in user dropbox with file list from spreadsheet
- Fix #1825: Remove close button from view new version popup
- Feat #1767: Restyle and reorder the buttons in create / edit dataset admin page

## v4.2.8 - 2024-05-28 - 771372fbb - live since 2024-05-30

- Feat #1832: Make script for calculating the md5 values and file sizes available for bastion users
- Fix #1817: generate mockup link button not appearing for all upload statuses
- Fix #1825: "Continue to view old version" closes New Version Alert pop up
- Feat #514: Add canonical URL to dataset pages

## v4.2.7 - 2024-05-07 - 902e5bfb5 - live since 2024-05-13

- Feat #1834: Create rclone config for bastion users to allow managing files on Wasabi
- Feat #1634, #1824: Add new EC2 server for file serving purposes (including author/reviewer accessible ftp server)
- Fix #1829: Change maintenance window for the daily backup to S3 to prevent DB server error on live web site
- Fix #1831: Update Out-of-date CSP on live blocks javascript scripts to prevent javascript errors

## v4.2.6 - 2024-04-30 - fe0348092 - live since 2024-05-02

- Feat #1394: Update bastion-users Ansible role to create AWS credentials file from Wasabi credentials CSV
- Fix #1833: Files metadata console containers built using gitlab-config-live-build.yml are tagged with "staging"

## v4.2.5 - 2024-04-25 - ca6d17e0f

- Feat #1770: Make AWS EFS can be mounted on the bastion server manually
- Feat #1783: Avoid URLs with double pagination in dataset url
- Feat #1757: Add pagination to samples table in dataset view
- Fix #1800: Read the correct sample page from cache on samples tables
- Feat #1163: Add carets to sortable headers in dataset page
- Feat #1664: Enable post upload script to work with non-published datasets

## v4.2.4 - 2024-04-16 - 576757f38 - live since 2024-04-17

- Docs #1791: Update IAM EC2 policy to prevent permission error when changing EC2 instant type on the fly
- Docs #1786: Add documentation for creating TLS certificate for AWS gigadb.org migration
- Fix #1784: Correct our configuration of PHP-FPM, Nginx and APCu to support real live load
- Feat #1221: EFS filesystem for GigaDB
- Feat #1717 #1715 #1716: remove Google Plus link, update X logo and add Mastodon link to social media links

## v4.2.3 - 2024-04-08 - f084f51dd

- Feat #1618: FUW - Allow customization of email sent to author after "DataPending" status is set
- Security #1626: FUW - Update node version to the latest recommended to avoid openssl hack. Minimize dependency vulnerabilities.
- Fix #1639: Paginate files and samples SQL queries on dataset pages to avoid running out of memory
- Fix #1593: Display the units of file size based on 1000 byte system

## v4.2.2 - 2024-03-25 - 3cf500fe8 - live since 2024-04-03

- Feat #1759: Replace Google Analytics script by Matomo script in all layouts
- Feat #1652: Update API doc for usage of retrieve known datasets by DOI endpoint

## v4.2.1 - 2024-03-18 - ac7d6168b

- Feat #1613: Add descriptive text, improved button labels and tooltips for user profile uploaded datasets tab
- Fix #1666: Update the datacite credentials and make the Mint DOI button working
- Fix #1230: Improve Excel upload tool by adding new dataset types in dev data
- Fix #1722: Show more button on Dataset:Sample admin page works after filtering
- Fix #1714: Visually hide long description in guide workflow, so that it's only visible to screen readers
- Fix #1657: Fix the broken tests from release 4.2.0, fix curation log form and spreadsheet upload consent checkbox
- Feat #1627: FUW - Migrate to Uppy version 2
- Feat #1629: FUW - Upgrade Element UI to latest version

## v4.2.0 - 2024-02-27 - 017ba8f58 - live since 2024-03-13

- Fix #1659: Remove dependency on abandoned inter-container-communication project and fix broken tests
- Feat #1624: FUW - Migrate to Vue 2.7
- Feat #1334: Improve accessibility of public and admin pages, revamp admin pages styles, sync style guide with updated styles

## v4.1.2 - 2024-02-07 - 9a421a2c - live since 2024-02-08

- Fix #1660: Ensure dataset pages that are not published are not publicly visible

## v4.1.1 - 2024-01-30 - 7bb35726 - live since 2024-02-01

- Fix #1527: Make the postUpload script use the image `production-files-metadata-console:production_environment` in the staging and live environments

## v4.1.0 - 2024-01-24 - d0495bee - live since 2024-01-26

- Fix #1654: Fix AdminDatasetForm acceptance tests
- Feat #1569: Display horizontal scroll bar in dataset files table when required
- Fix #1645: Fix failing ResetPasswordCest for FUW
- Feat #588: Re-enable the old, initial work on File Upload Wizard behind a new Gitlab-based feature flag
- Feat #1384: Improve accessibility of login page: input focus state, form errors, aria labels, required

## v4.0.5 - 2024-01-02 - d4deab10 - live since 2024-01-09

- Feat #1595: Update total volume of data on home page
- Fix #1529: Reload the same admin form page after save for private dataset
- Feat #1259: Document how to update dataset spreadsheet template

## v4.0.4 - 2023-12-11 - 4c91cfc

- Feat #1586: Make terraform always look for the latest CentOS Stream 8 x86_64 AMI
- Feat #1530: Implement scenario for checking the updated values in the mockup page

## v4.0.3 - 2023-12-04 - c2869d0

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
