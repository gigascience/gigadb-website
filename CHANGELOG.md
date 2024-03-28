# GigaDB Changelog

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Unreleased

- Feat #1664: Enable post upload script to work with non-published datasets

## v4.2.2 - 2024-03-25 - 3cf500fe8

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
