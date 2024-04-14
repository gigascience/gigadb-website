# GitLab variables

## GROUP: gigascience

The root group is [gigascience](https://gitlab.com/gigascience). There are no 
GitLab variables at this level.

## SUB-GROUP: Upstream

The [Upstream](https://gitlab.com/gigascience/upstream) sub-group contains the 
following variables that can be used by all projects:

| Variable | Example value | Used in | Comments |
|----------|---------------|---------|----------|
| AWS_ACCESS_KEY_ID | | File upload wizard | Live and staging deployments use same AWS IAM user account |
| AWS_SECRET_ACCESS_KEY | | File upload wizard | As above |
| AWS_DEFAULT_REGION | | | Decide on AWS region to use for live and staging deployments |
| CSV_DIR | | csv-to-migrations to control what data to use in database migrations | Set in Upstream as won't change between environments |
| DB_BACKUP_HOST | parrot.* | params.php.dist by files-url-updater tool | As above |
| DB_BACKUP_PASSWORD | **** | params.php.dist by files-url-updater tool | As above |
| DB_BACKUP_USERNAME | user***| params.php.dist by files-url-updater tool | As above |
| DOCKER_HUB_PASSWORD | | gitlab-build-jobs.yml | As above |
| DOCKER_HUB_USERNAME | | gitlab-build-jobs.yml | As above |
| EXPORT_CSV_GIGADB_DB | | export_csv.sh | As above |
| EXPORT_CSV_GIGADB_HOST | | export_csv.sh | As above |
| EXPORT_CSV_GIGADB_USER | | export_csv.sh | As above |
| EXPORT_CSV_GIGADB_PASSWORD | | export_csv.sh | As above |
| FORK | gigascience | NewsletterTest.php | As above |
| FTP_CONNECTION_URL | | main.php.dist | As above |
| GITLAB_PRIVATE_TOKEN | | GitLab API authentication | As above |
| URL_PREFIX | | File location URL updates | As above |

## PROJECT: gigascience > Upstream > gigadb-website

This project level currently only contains the reference 
gigascience/gigadb-website codebase which is used for deploying 
staging.gigadb.org and gigadb.org. It contains the following variables whose
values can differ depending on deployment environment:

| Variable | Example value | Used in | Comments |
|----------|---------------|---------|----------|
| ANALYTICS_CLIENT_EMAIL | | local.php.dist for Google analytics  | Use 2 Google analytics accounts, one for live GigaDB deployment and one for staging deployment |
| ANALYTICS_CLIENT_ID | | local.php.dist | As above |
| ANALYTICS_KEYFILE_PATH | | Cannot find where it is used | Keep for now in line with above |
| ANALYTICS_PRIVATE_KEY | | docker-compose.ci.yml | As above |
| AWS_S3_BUCKET_FOR_FILE_BUNDLES | | File preview | Different buckets for live and staging deployments |
| AWS_S3_BUCKET_FOR_FILE_PREVIEWS | | File preview | As above |
| BEANSTALK_SERVER_HOST | | File preview | Different for live and staging deployments |
| COVERALLS_REPO_TOKEN | | Used by coveralls test coverage tool | Different for live and staging deployments? |
| DEPLOYMENT_ENV | live / staging | gigadb-build-jobs.yml, gigadb-deploy-jobs.yml, gigadb-operations-jobs.yml | Different for live and staging deployments |
| Facebook_access_token | | Affiliate login | Set at project-level with real Facebook account for live GigaDB deployment but use test Facebook accounts for staging |
| FACEBOOK_APP_ID | | main.php.dist, Affiliate login | As above |
| FACEBOOK_APP_SECRET | | main.php.dist, Affiliate login | As above |
| Facebook_tester_email | | Affiliate login tests | Set at project-level with same value for live.gigadb.org and staging.gigadb.org |
| Facebook_tester_first_name | | Affiliate login tests | As above |
| Facebook_tester_password | | Affiliate login tests | As above |
| fuw_db_database | | | Set at project-level with same value for staging and live deployments |
| fuw_db_host | | | Set at project-level with different value for staging and live deployments |
| FUW_DB_HOST | | | Set at project-level with different value for staging and live deployments |
| FUW_DB_NAME | | | Set at project-level with same value for staging and live deployments |
| fuw_db_password | | | Set at project-level with same value for staging and live deployments |
| FUW_DB_PASSWORD | | | Set at project-level with same value for staging and live deployments |
| fuw_db_user | | | Set at project-level with same value for staging and live deployments |
| FUW_DB_USER | | | Set at project-level with same value for staging and live deployments |
| FUW_FTP_HOST | | | Set at project-level with different value for staging and live deployments |
| FUW_JWT_KEY | | | Set at project-level with same value for staging and live deployments |
| FUW_TESTDB_HOST | | | Set at project-level with different value for staging and live deployments |
| FUW_TESTDB_NAME | | | Set at project-level with same value for staging and live deployments |
| FUW_TESTDB_PASSWORD | | | Set at project-level with same value for staging and live deployments |
| FUW_TESTDB_USER | | | Set at project-level with same value for staging and live deployments |
| GIGADB_admin_tester_email | | GigadbWebsiteContext.php admin behat tests | Keep at project-level because other login test variables are here but can have same value for staging and live deployments |
| GIGADB_admin_tester_first_name | | GigadbWebsiteContext.php admin behat tests | As above |
| GIGADB_admin_tester_last_name | | GigadbWebsiteContext.php admin behat tests | As above |
| GIGADB_admin_tester_password | | GigadbWebsiteContext.php admin behat tests | As above |
| GIGADB_DB | | functional, behat tests, db.json.dist | Set at project-level with different value for staging and live |
| gigadb_db_database | | functional, behat tests, db.json.dist | Set at project-level with same value for staging and live but realistically probably be the same |
| gigadb_db_host | | functional, behat tests, db.json.dist | Set at project-level with different value for staging and live |
| gigadb_db_password | | functional, behat tests, db.json.dist | Set at project-level with same value for staging and live |
| gigadb_db_user | | functional, behat tests, db.json.dist | Set at project-level with same value for staging and live but realistically probably be the same |
| GIGADB_HOST | | functional, behat tests, db.json.dist | Set at project-level with different value for staging and live |
| GIGADB_PASSWORD | | functional, behat tests, db.json.dist | Set at project-level with same value for staging and live |
| GIGADB_USER | | functional, behat tests, db.json.dist | Set at project-level with same value for staging and live but realistically probably be the same |
| GOOGLE_ANALYTICS_PROFILE | | local.php.dist | Set at project-level with real Analytics account for live GigaDB deployment but use test Analytics accounts for staging |
| GOOGLE_API_AUTH_CODE | | local.php.dist | As above |
| GOOGLE_CLIENT_ID | | main.php.dist, Affiliate login tests | As above |
| GOOGLE_SECRET | | main.php.dist, Affiliate login tests | As above |
| Google_tester_email | | Affiliate login tests | Set at project-level with same value for live.gigadb.org and staging.gigadb.org |
| Google_tester_first_name | | Affiliate login tests | As above |
| Google_tester_last_name | | Affiliate login tests | As above |
| Google_tester_password | | Affiliate login tests | As above |
| HOME_URL | http://gigadb.gigasciencejournal.com:9170 | local.php.dist, help.html.dist | Set at project-level with different value for staging and live |
| LINKEDIN_API_KEY | | main.php.dist, Affiliate login tests | Set at project-level with real account for gigadb.org but use test account for staging.gigadb.org |
| LINKEDIN_SECRET_KEY | | main.php.dist, Affiliate login tests | As above |
| LinkedIn_tester_email | | Affiliate login tests | Set at project-level with same value for gigadb.org and staging.gigadb.org |
| LinkedIn_tester_first_name | | Affiliate login tests | As above |
| LinkedIn_tester_last_name | | Affiliate login tests | As above |
| LinkedIn_tester_password | | Affiliate login tests | As above |
| LIVE_IP_ADDRESS | | Cannot find where it is used | Kept in project-level for live deployment |
| live_tlsauth_ca | |  | Automatically set in project-level for live deployment by Ansible|
| live_tlsauth_cert | |  | As above |
| live_tlsauth_key | |  | As above |
| MAILCHIMP_API_KEY | | local.php.dist, NewsletterTest.php | Set at project-level with real accounts for gigadb.org but use test account for staging.gigadb.org |
| MAILCHIMP_LIST_ID | | local.php.dist, NewsletterTest.php | As above |
| MAILCHIMP_TEST_EMAIL | | local.php.dist, NewsletterTest.php | As above |
| MDS_PASSWORD | | local.php.dist for minting DOIs | Set at project-level with real account for gigadb.org but use test account for staging.gigadb.org |
| MDS_PREFIX | | local.php.dist for minting DOIs | As above |
| MDS_USERNAME | | local.php.dist for minting DOIs | As above |
| MULTIDOWNLOAD_SERVER_HOST | | Multi download functionality | Set at project-level with different hosts for gigadb.org and staging.gigadb.org |
| OPAUTH_SECURITY_SALT | | main.php.dist for opauth package | Set at project-level with same value for gigadb.org and for staging.gigadb.org |
| ORCID_CLIENT_ENVIRONMENT | | main.php.dist for affiliate login | Set at project-level with real account for gigadb.org but use test account for staging.gigadb.org |
| ORCID_CLIENT_ID | | main.php.dist, Affiliate login tests | As above |
| ORCID_CLIENT_SECRET | | main.php.dist, Affiliate login tests | As above |
| Orcid_tester_email | | Affiliate login tests | Set at project-level with same value for gigadb.org and staging.gigadb.org |
| Orcid_tester_first_name | | Affiliate login tests | As above|
| Orcid_tester_last_name | | Affiliate login tests | As above |
| Orcid_tester_password | | Affiliate login tests | As above |
| Orcid_tester_uid | | Affiliate login tests | As above |
| PORTAINER_PASSWORD | | | Set at project-level with same values for staging and live deployments |
| PREVIEW_SERVER_HOST | | preview functionality | Set at project-level with different hosts for staging and live deployments |
| RECAPTCHA_PRIVATEKEY | | local.php.dist for login captcha test | Set at project-level with same values for staging and live deployments |
| RECAPTCHA_PUBLICKEY | | local.php.dist for login captcha test | As above |
| REDIS_SERVER_HOST | | multi download functionality | Set at project-level with different values for staging and live deployments |
| REMOTE_FILES_PUBLIC_URL | https://gigadb.net/datasetfiles | | Set at project-level with different values for staging and live deployments |
| REMOTE_FUW_DB_HOST | | used by fuw | Set at project-level with different values for staging and live deployments |
| REMOTE_FUW_DB_NAME | | used by fuw | Set at project-level with same value for staging and live deployments |
| REMOTE_FUW_DB_PASSWORD | | used by fuw | Set at project-level with different values for staging and live deployments |
| REMOTE_FUW_DB_USER | | used by fuw | Set at project-level but probably same value for staging and live deployments |
| REMOTE_GIGADB_HOST | | used by fuw | Set at project-level with different value for staging and live deployments |
| REMOTE_GIGADB_DB | | used by fuw | Set at project-level with same value for staging and live deployments |
| REMOTE_GIGADB_PASSWORD | | used by fuw | As above |
| REMOTE_GIGADB_USER | | used by fuw | As above |
| REMOTE_HOME_URL | https://staging.gigadb.net | | Set at project-level with different values for staging and live deployments |
| REMOTE_HOSTNAME | staging.gigadb.net | | Set at project-level with different values for staging and live deployments |
| remote_private_ip | | | Automatically created for live and staging deployments |
| remote_public_ip | | | As above |
| REMOTE_SMTP_HOST | | main.php.dist | Set at project-level with different values for staging and live deployments |
| REMOTE_SMTP_PASSWORD | | main.php.dist | Set at project-level with same values for staging and live deployments |
| REMOTE_SMTP_PORT | | main.php.dist | As above |
| REMOTE_SMTP_USERNAME | | main.php.dist | As above |
| SERVER_EMAIL | | test.php.dist, local.php.dist, web.php.dist | Set in at project-level so live server has specific email sending functionality. Use test@gigasciencejournal.com for staging |
| SERVER_EMAIL_SMTP_HOST | | test.php.dist, web.php.dist | Set at project-level with different values for staging and live deployments |
| SERVER_EMAIL_PASSWORD | | test.php.dist, web.php.dist | Set at project-level with same values for staging and live deployments |
| SERVER_EMAIL_SMTP_PORT | | test.php.dist, web.php.dist | As above |
| STAGING_IP_ADDRESS | | Cannot find where it is used | Kept in project-level |
| staging_tlsauth_ca | |  | Automatically set in project-level for staging deployment by Ansible|
| staging_tlsauth_cert | |  | As above |
| staging_tlsauth_key | |  | As above |
| TENCENTCLOUD_APP_ID | | dataset-backup-tool | Set at project level so live server has the original CNGB Tencent account and staging has Peter's test Tencent account.  |
| TENCENTCLOUD_SECRET_ID | | As above | As above |
| TENCENTCLOUD_SECRET_KEY | | As above | As above |
| TWITTER_KEY | | main.php.dist, Affiliate login tests | Set at project-level with real account for gigadb.org but use test account for staging.gigadb.org |
| TWITTER_SECRET | | main.php.dist, Affiliate login tests | As above |
| Twitter_tester_email | | Affiliate login tests | Set at project-level with same value for gigadb.org and staging.gigadb.org and at Forks sub-group levels |
| Twitter_tester_password | |Affiliate login tests | As above |
| Twitter_tester_first_name | | Affiliate login tests | As above |
| Twitter_tester_last_name | | Affiliate login tests | As above |
| PHP_APCU_MEMORY | | Production-Dockerfile | Size of in memory cache |
| PHP_FPM_MAX_CHILDREN | | Production-Dockerfile | The maximum number of child processes | 
| PHP_FPM_START_SERVERS | | Production-Dockerfile | The number of child processes created on startup |
| PHP_FPM_MIN_SPARE_SERVERS | | Production-Dockerfile | The desired minimum number of idle server processes |
| PHP_FPM_MAX_SPARE_SERVERS | | Production-Dockerfile | The desired maximum number of idle server processes |
| PHP_CONN_LIMIT| enabled or disabled | | Whether rate limit is enabled for PHP requests | 

## SUB-GROUP: Forks

[Forks](https://gitlab.com/gigascience/forks) is the second sub-group in the 
[gigascience](https://gitlab.com/gigascience) root group. It currently contains 
the following variables which all projects belonging to developers in the Forks 
sub-group can use:

| Variable                       | Example value | Used in                                                        | Comments                                                                                                            |
|--------------------------------|--------------|----------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------|
| ANALYTICS_CLIENT_EMAIL         | | local.php.dist for Google analytics                            | Set at Forks level with test Google Analytics account for development work                                          |
| ANALYTICS_CLIENT_ID            | | local.php.dist                                                 | As above                                                                                                            |
| ANALYTICS_KEYFILE_PATH         | | Cannot find where it is used                                   | As above                                                                                                            |
| ANALYTICS_PRIVATE_KEY          | | docker-compose.ci.yml                                          | As above                                                                                                            |
| Facebook_access_token          | | Affiliate login                                                | Use test Facebook account for development work                                                                      |
| FACEBOOK_APP_ID                | | Affiliate login                                                | As above                                                                                                            |
| FACEBOOK_APP_SECRET            | | Affiliate login                                                | As above                                                                                                            |
| Facebook_tester_email          | | Affiliate login                                                | As above                                                                                                            |
| Facebook_tester_first_name     | | Affiliate login                                                | As above                                                                                                            |
| Facebook_tester_last_name      | | Affiliate login                                                | As above                                                                                                            |
| Facebook_tester_password       | | Affiliate login                                                | As above                                                                                                            |
| GIGADB_admin_tester_email      | | GigadbWebsiteContext.php admin behat tests                     | Set at Forks sub-group levels                                                                                       |
| GIGADB_admin_tester_first_name | | GigadbWebsiteContext.php admin behat tests                     | As above                                                                                                            |
| GIGADB_admin_tester_last_name  | | GigadbWebsiteContext.php admin behat tests                     | As above                                                                                                            |
| GIGADB_admin_tester_password   | | GigadbWebsiteContext.php admin behat tests                     | As above                                                                                                            |
| GOOGLE_ANALYTICS_PROFILE       | | local.php.dist                                                 | Set at Forks sub-group level so developers use test account                                                         |
| GOOGLE_API_AUTH_CODE           | | local.php.dist                                                 | As above                                                                                                            |
| GOOGLE_CLIENT_ID               | | main.php.dist, Affiliate login tests                           | As above                                                                                                            |
| GOOGLE_SECRET                  | | main.php.dist, Affiliate login tests                           | As above                                                                                                            |
| Google_tester_email            | | Affiliate login tests                                          | Set at Forks sub-group level so all developers use same credentials                                                 |
| Google_tester_first_name       | | Affiliate login tests                                          | As above                                                                                                            |
| Google_tester_last_name        | | Affiliate login tests                                          | As above                                                                                                            |
| Google_tester_password         | | Affiliate login tests                                          | As above                                                                                                            |
| group                          | Forks | Cannot find where it is used                                   | Kept as a Forks sub-group variable                                                                                  |
| HASH_SECRET_KEY                | | local.php.dist                                                 | Used as a signing key for creating the hash value of the password reset token verifier                              |
| LINKEDIN_API_KEY               | | main.php.dist, Affiliate login tests                           | Set at Forks sub-group level so all developers use same test LinkedIn account for development work                  |
| LINKEDIN_SECRET_KEY            | | main.php.dist, Affiliate login tests                           | As above                                                                                                            |
| LinkedIn_tester_email          | | Affiliate login tests                                          | As above                                                                                                            |
| LinkedIn_tester_password       | | Affiliate login tests                                          | As above                                                                                                            |
| LinkedIn_tester_first_name     | | Affiliate login tests                                          | As above                                                                                                            |
| LinkedIn_tester_last_name      | | Affiliate login tests                                          | As above                                                                                                            |
| MAILCHIMP_API_KEY              | | local.php.dist, NewsletterTest.php                             | Set at Forks sub-group level so all developers use same test Mailchimp account for development work                 |
| MAILCHIMP_LIST_ID              | | local.php.dist, NewsletterTest.php                             | As above                                                                                                            |
| MAILCHIMP_TEST_EMAIL           | | local.php.dist, NewsletterTest.php                             | As above                                                                                                            |
| MDS_PASSWORD                   | | local.php.dist for minting DOIs                                | Set at Forks sub-group level so all developers use same test MDS account for development work                       |
| MDS_PREFIX                     | | local.php.dist for minting DOIs                                | As above                                                                                                            |
| MDS_USERNAME                   | | local.php.dist for minting DOIs                                | As above                                                                                                            |
| MDS_DOI_URL                    | | local.php.dist for minting DOIs                                | As above                                                                                                            |
| MDS_MEADATA_URL                | | local.php.dist for minting DOIs                                | As above                                                                                                            |
| OPAUTH_SECURITY_SALT           | | main.php.dist for opauth package                               | Set in Forks sub-group with test OPAUTH_SECURITY_SALT for development work                                          |
| ORCID_CLIENT_ENVIRONMENT       | | main.php.dist for affiliate login                              | Set at Forks sub-group level so all developers use test ORCID account for development work                          |
| ORCID_CLIENT_ID                | | main.php.dist, Affiliate login tests                           | As above                                                                                                            |
| ORCID_CLIENT_SECRET            | | main.php.dist, Affiliate login tests                           | As above                                                                                                            |
| Orcid_tester_email             | | Affiliate login tests                                          | As above                                                                                                            |
| Orcid_tester_first_name        | | Affiliate login tests                                          | As above                                                                                                            |
| Orcid_tester_last_name         | | Affiliate login tests                                          | As above                                                                                                            |
| Orcid_tester_password          | | Affiliate login tests                                          | As above                                                                                                            |
| Orcid_tester_uid               | | Affiliate login tests                                          | As above                                                                                                            |
| RECAPTCHA_PRIVATEKEY           | | local.php.dist for login captcha test                          | Set at Forks sub-group level so all developers use same development specific RECAPTCHA account for development work |
| RECAPTCHA_PUBLICKEY            | | local.php.dist for login captcha test                          | As above                                                                                                            |
| TWITTER_KEY                    | | main.php.dist, Affiliate login tests                           | Set at Forks sub-group level so all developers use same test Twitter account for development work                   |
| TWITTER_SECRET                 | | main.php.dist, Affiliate login tests                           | As above                                                                                                            |
| Twitter_tester_email           | | Affiliate login tests                                          | As above                                                                                                            |
| Twitter_tester_password        | | Affiliate login tests                                          | As above                                                                                                            |
| Twitter_tester_first_name      | | Affiliate login tests                                          | As above                                                                                                            |
| Twitter_tester_last_name       | | Affiliate login tests                                          | As above                                                                                                            |
| GITTER_API_TOKEN               | | API token to access Gitter API                                 | Use personal token on dev forks, tech@ token on Upstream                                                            |
| GITTER_IT_NOTIFICATION_ROOM_ID | | Numeric ID of a specific Gitter room (the IT notification one) |                                                                                                             |
| URL_PREFIX                     | | File location URL updates | As above |

## PROJECT: *-gigadb-website

Your fork of the gigadb-website codebase is represented as a GitLab project in 
Forks sub-group and should contain the following variables whose values may
differ between developers:

| Variable | Example value | Used in | Comments |
|----------|---------------|---------|----------|
| AWS_ACCESS_KEY_ID | | File upload wizard | Set at project-level so developers use own AWS IAM user credentials |
| AWS_DEFAULT_REGION | | | As above |
| AWS_SECRET_ACCESS_KEY | | File upload wizard | As above |
| AWS_S3_BUCKET_FOR_FILE_BUNDLES | | File preview | Set at project-level so developers use own S3 bucket |
| AWS_S3_BUCKET_FOR_FILE_PREVIEWS | | File preview | As above |
| BEANSTALK_SERVER_HOST | | File preview | Set at project-level |
| COVERALLS_REPO_TOKEN | | Used by coveralls test coverage tool | Set at project-level |
| CSV_DIR | | Used by csv-to-migrations to control what data to use in database migrations | Set at project-level since it can differ between developers |
| DB_BACKUP_HOST | parrot.* | params.php.dist by files-url-updater tool | Set in project-level in case developer wants to use another FTP server |
| DB_BACKUP_PASSWORD | **** | params.php.dist by files-url-updater tool | As above |
| DB_BACKUP_USERNAME | user***| params.php.dist by files-url-updater tool | As above |
| DEPLOYMENT_ENV | live / staging | gigadb-build-jobs.yml, gigadb-deploy-jobs.yml, gigadb-operations-jobs.yml | Set at project level because it requires different values for live and staging environments |
| DOCKER_HUB_PASSWORD | | gitlab-build-jobs.yml | Set at project-level so developer uses their own credetials |
| DOCKER_HUB_USERNAME | | gitlab-build-jobs.yml | As above |
| EXPORT_CSV_GIGADB_DB | | export_csv.sh | Differs between developer, set at project-level |
| EXPORT_CSV_GIGADB_HOST | | export_csv.sh | As above |
| EXPORT_CSV_GIGADB_USER | | export_csv.sh | As above |
| FORK | pli888 | NewsletterTest.php | Differs between developer, set at project-level |
| FTP_CONNECTION_URL | | main.php.dist | Set at project-level for developers |
| fuw_db_database | | | Differs between developer, set at project-level |
| fuw_db_host | | | As above |
| fuw_db_password | | | As above |
| fuw_db_user | | | As above |
| FUW_DB_HOST | | File upload wizard | Goes in project-level as can differ between developers |
| FUW_DB_NAME | | File upload wizard | As above |
| FUW_DB_PASSWORD | | File upload wizard | As above |
| FUW_DB_USER | | File upload wizard | As above |
| FUW_FTP_HOST | | File upload wizard | As above |
| FUW_JWT_KEY | | File upload wizard | As above |
| FUW_TESTDB_HOST | | test-local.php.dist by fuw apps | As above |
| FUW_TESTDB_NAME | | test-local.php.dist by fuw apps | As above |
| FUW_TESTDB_PASSWORD | |test-local.php.dist by fuw apps | As above |
| FUW_TESTDB_USER | | test-local.php.dist by fuw apps | As above |
| gigadb_db_database | | Used by Ansible | Differs between developer, set at project-level |
| gigadb_db_host | | | As above |
| gigadb_db_password | | | As above |
| gigadb_db_user | | | As above |
| GIGADB_DB | | functional, behat tests, db.json.dist | Differs between developer, set at project-level |
| GIGADB_HOST | | functional, behat tests, db.json.dist | As above |
| GIGADB_PASSWORD | |functional, behat tests, db.json.dist | As above |
| GIGADB_USER | | functional, behat tests, db.json.dist | As above |
| GITLAB_PRIVATE_TOKEN | | | Differs for each developer so set at project-level |
| HOME_URL | http://gigadb.gigasciencejournal.com:9170 | local.php.dist, help.html.dist | Might differ for each developer so set at project-level |
| MULTIDOWNLOAD_SERVER_HOST | | Multi download functionality | Might differ for each developer so set at project-level |
| PORTAINER_PASSWORD | | Authentication to Portainer Docker UI | Differs between developer, set at project-level - realistically use same value for all environments |
| PREVIEW_SERVER_HOST | | preview functionality | As above |
| REDIS_SERVER_HOST | | multi download functionality | As above |
| REMOTE_FILES_PUBLIC_URL | https://gigadb.net/datasetfiles | Public URL to reversed proxied tusd server used by FUW | Set at project-level as this will differ between developers |
| REMOTE_FUW_DB_HOST | | used by fuw | Differs between developer, set at project-level - realistically use same value for all environments |
| REMOTE_FUW_DB_NAME | | used by fuw | As above |
| REMOTE_FUW_DB_PASSWORD | | used by fuw | As above |
| REMOTE_FUW_DB_USER | | used by fuw | As above |
| REMOTE_GIGADB_DB | | Cannot find where it is used |
| REMOTE_GIGADB_HOST | | Cannot find where it is used |
| REMOTE_GIGADB_PASSWORD | | Cannot find where it is used |
| REMOTE_GIGADB_USER | | Cannot find where it is used |
| REMOTE_SMTP_HOST | | main.php.dist | As above |
| REMOTE_SMTP_PASSWORD | | main.php.dist | As above |
| REMOTE_SMTP_PORT | | main.php.dist | As above |
| REMOTE_SMTP_USERNAME | | main.php.dist | As above |
| REMOTE_PUBLIC_HTTP_PORT | 80 | docker-compose.production-envs.yml | Set at project-level|
| REMOTE_PUBLIC_HTTPS_PORT | 443 | docker-compose.production-envs.yml | As above |
| SERVER_EMAIL | | test.php.dist, local.php.dist, web.php.dist | Set in at project-level so developer can use their own email provider of choice |
| SERVER_EMAIL_PASSWORD | | test.php.dist, web.php.dist | As above |
| SERVER_EMAIL_SMTP_HOST | | test.php.dist, web.php.dist | As above |
| SERVER_EMAIL_SMTP_PORT | | test.php.dist, web.php.dist | As above |
| TENCENTCLOUD_APP_ID | | dataset-backup-tool | Set at project level so developers can use their own Tencent account |
| TENCENTCLOUD_SECRET_ID | | As above | As above |
| TENCENTCLOUD_SECRET_KEY | | As above | As above |
| PHP_APCU_MEMORY | | Production-Dockerfile | Size of in memory cache |
| PHP_FPM_MAX_CHILDREN | | Production-Dockerfile | The maximum number of child processes | 
| PHP_FPM_START_SERVERS | | Production-Dockerfile | The number of child processes created on startup |
| PHP_FPM_MIN_SPARE_SERVERS | | Production-Dockerfile | The desired minimum number of idle server processes |
| PHP_FPM_MAX_SPARE_SERVERS | | Production-Dockerfile | The desired maximum number of idle server processes |
| PHP_CONN_LIMIT| enabled or disabled | | Whether rate limit is enabled for PHP requests | 

### Automatically-created variables in PROJECT: *-gigadb-website

| Variable | Example value | Used in | Comments |
|----------|---------------|---------|----------|
| docker_tlsauth_ca | | Certificate authority for Dockerhost | Created by Ansible |
| docker_tlsauth_cert | | Public certificate for Dockerhost | As above |
| docker_tlsauth_key | | Server key for above CA | As above |
| remote_private_ip | | Private IP of Dockerhost | Created by Ansible |
| remote_public_ip | | Public IP of Dockerhost | As above |
| tls_chain_pem | | Contains additional intermediate certificate or certificates that web browsers need to validate server certificate | Created by Gitlab job |
| tls_fullchain_pem | | All certificates, including server certificate (aka leaf certificate or end-entity certificate). The server certificate is the first one in this file, followed by any intermediates. | As above | |
| tls_privkey_pem | | Private key for certificate in PEM format | As above | |

## Configuration variables that are deprecated and are no longer to be used

Keep them listed here in case there are some usage left in code, we need to know
they should not be used when we stumble upon them

* TLSAUTH_CA
* TLSAUTH_CERT
* TLSAUTH_KEY
* STAGING_IP_ADDRESS

## File: secrets-sample

The above variables are retrieved from GitLab in a step within the `up.sh` 
script. The [secrets-sample](../ops/configuration/variables/secrets-sample) 
file provides a template listing of these variables.

# Docker environment variables

Variables used to configure the Docker environment are set in a `.env` file. A
sample of these variables can be found in [env-sample](../ops/configuration/variables/env-sample).
