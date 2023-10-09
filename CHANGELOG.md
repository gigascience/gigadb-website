# GigaDB Changelog

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Unreleased

- Feat #1471: Improve Accessibility for Conditional Form Inputs Based on "Image Status"
- Feat #1474: Admin styles - Update styles of Manage Dataset Authors page
- Fix #1428: Increase resilience of provisioning by extracting saving EC2 IP addresses as standalone bootstrap plays
- Feat #1491: Admin styles - Style user admin page
- Feat #1481: Improve appearance of dataset form action buttons
- feat #1434: Setup playwright local environment for automated accessibility testing
- Feat #1487: Use modern styles in Admin View DatasetAuthor page
- Feat #1485: Admin styles - Update styles of Manage Dataset Samples page
- Feat #1482: Implement modern styles in admin Create DatasetAuthor Page
- Feat #1474: Admin styles - Update styles of Manage Dataset Authors page
- Feat #1465: Update adminDataset/create and adminDataset/update pages styles to modern look
- Feat #1462: Update adminDataset/admin page styles to modern look
- Feat #1460: Update admin dashboard styles to modern look
- Feat #1375: Improve accessibility of dataset search pagination
- Feat #1432: Improve semantics of breadcrumbs sections and other minor breadcrumb-related tweaks
- Feat #1378: Improve accessibility of help page
- Feat #1372: Fix accessibility issues related to search page headings and hero layout
- Feat #1371: Essential accessibility fixes on dataset page
- Feat #1367: Improve accessibility of dataset type and metric sections in homepage
- Feat #1366: Enhance text over image contrast in hero banner
- Feat #1384: Improve accessibility of login page: input focus state, form errors, aria labels, required
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