# SOP: Update software's major version for gigadb website

This sop is about how to update software's major version for the gigadb website.
The file `ops/configuration/variables/env-sample` stores the currently using version for the dev environment, while the file `.gitlab-ci.yml` stores the currently using version for the production environments,
any changes in the version should be done in the correct file according to the requirement.

### Dev environment

##### Update PostgreSQL engine and client major version

In `ops/configuration/variables/env-sample`, update the value of the variable `POSTGRES_VERSION`, for example:
```
POSTGRES_VERSION=14.8
```
And accordingly update the `postgresql client` version in different service's Dockerfile based on the `ops/deployment/docker-compose.yml` file, see the below table for details:


| Services      | Dockerfile                                                     | INSTALL_PG_CLIENT | current pg_client version |
|---------------|----------------------------------------------------------------|-------------------|---------------------------|
| test          | ops/packaging/Dockerfile                                       | true              | postgresql-client-14      | 
| application** | ops/packaging/Dockerfile                                       | false             | postgresql-client-14      |
| console       | fuw/app/common/Dockerfile                                      | true              | postgresql-client-14      |
| tool          | gigadb/app/tools/readme-generator/Dockerfile                   | true              | postgresql-client-14      |
| pg_client     | gigadb/app/tools/excel-spreadsheet-uploader/PgClientDockerfile | true              | postgresql-client-14      |

**: postgresql client will not be installed by default, updates in Dockerfile to make it ready for future use.

The following commands can be used to update the versions of postgreSQL engine and postgreSQL client, and validate if the version is updated.
```
# get the updated version number
% cp ops/configuration/variables/env-sample .env
# spin up gigadb website, 
./up.sh
# sql/gigadb.pgdm will be created by pg_dump version 14.8
# check there is a directory is created for the updated database version  
% ls ~/.containers-data/default-gigadb/postgres
14.8
# check the postgresql engine version in database service
% docker-compose run --rm database bash
Creating deployment_database_run ... done
77c1063b671c:/# psql --version
psql (PostgreSQL) 14.8
77c1063b671c:/# pg_dump --version
pg_dump (PostgreSQL) 14.8
77c1063b671c:/# 
% docker-compose run --rm console bash
Creating deployment_console_run ... done
root@f53581214658:/app# psql --version
psql (PostgreSQL) 14.8 (Debian 14.8-1.pgdg100+1)
root@f53581214658:/app# pg_dump --version
pg_dump (PostgreSQL) 14.8 (Debian 14.8-1.pgdg100+1)
% docker-compose run --rm test bash -c "psql --version"
Creating deployment_test_run ... done
psql (PostgreSQL) 14.8 (Debian 14.8-1.pgdg100+1)
% docker-compose run --rm test bash -c "pg_dump --version"
Creating deployment_test_run ... done
pg_dump (PostgreSQL) 14.8 (Debian 14.8-1.pgdg100+1)
# Restore the dump database created by pg_dump version 14.8
 % docker-compose run --rm test bash -c "pg_restore --version"
Creating deployment_test_run ... done
pg_restore (PostgreSQL) 14.8 (Debian 14.8-1.pgdg100+1)
% docker-compose run --rm test bash -c "pg_restore -h database -p 5432 -U gigadb -d gigadb --clean --no-owner -v sql/gigadb.pgdmp"
```

##### Update Yii version

The Yii framework version release information can be found at [here](https://www.yiiframework.com/news?tag=release)

In `ops/configuration/variables/env-sample`, update the value of the variables `YII_VERSION` and `YII2_VERSION`, for example:
```
YII_VERSION=1.1.28
YII2_VERSION=2.0.48
```

The `composer.lock` file specifies the versions of all packages, the following commands can be used to update a specific package after `./up.sh`, and validate if the version is updated.
```
# get the latest updated
% cp ops/configuration/variables/env-sample .env
# generate the composer.json file
% docker-compose run --rm config
# perform manual upgrade for the gigadb main services
% docker-compose exec -T application composer require yiisoft/yii:"~1.1.28"
% docker-compose exec -T application composer require yiisoft/yii2:"~2.0.48"
% docker-compose run --rm console bash -c 'composer require yiisoft/yii2:"~2.0.48"'
# only yii1 and yii2 in the composer.lock will be updated
# verfiy the composer.lock is working 
% docker-compose exec -T application composer install
# the above command will only perform install according to the lock file
# if there is composer error, or the upgrade is no longer wanted, can restore the composer.lock to its previous state using
% git restore composer.lock
# if there is no error and works as ecpected, commit the updated composer.lock to github 
# spin up the gigadb website again
% ./up.sh
# scroll throught the scrren output log, the migration jobs are performed by the following tools
...
Yii Migration Tool v1.0 (based on Yii v1.1.28)
Yii Migration Tool (based on Yii v2.0.48)
...
```

Be aware that `composer update` will update all packages in the lock to their latest version, unless it is your intention to do so.


### Production environments

##### Warning!

Before you are going to update software's major version in the `Upstream`, make sure you have:

1. Reminded gigadb.org users of the downtime issue, because services would be temporarily suspended during the update process.
2. Confirmed that the most recent backup database  exists in the bucket `s3:gigadb-database-backups`, eg. `gigadb_gigascience-upstream-gigadb-website_live_"$latestDate".backup`, where `$latestDate` is `current date - 1`.
3. Confirmed that the backup database is not corrupt and the restoration steps can be executed successfully in developer's production environments. 

##### Update PostgreSQL engine and client major version

In `.gitlab-ci.yml`, update the value of the variable `POSTGRES_VERSION`, for example:
```
POSTGRES_VERSION: "14.8"
```
And accordingly update the `postgresql client` version in different service's Dockerfile based on the `ops/deployment/docker-compose.build.yml` and `ops/deployment/docker-compose.production-envs.yml` files,
see the below table for details:

| Services               | Dockerfile                                                     | INSTALL_PG_CLIENT | current pg_client version |
|------------------------|----------------------------------------------------------------|-------------------|---------------------------|
| production_fuw-console | fuw/app/common/Production-Dockerfile                           | true              | postgresql-client-14      |
| production_app**       | ops/packaging/Production-Dockerfile                            | false             | postgresql-client-14      |
| production_tool        | gigadb/app/tools/readme-generator/Dockerfile-Production        | true              | postgresql-client-14      |
| production_pgclient    | gigadb/app/tools/excel-spreadsheet-uploader/PgClientDockerfile | true              | postgresql-client-14      |
| production_s3backup    | gigadb/app/tools/files-url-updater/S3BackupDockerfile          | true              | postgresql-client-14      |

**: postgresql client will not be installed by default, updates in Dockerfile to make it ready for future use. 

##### Update AWS bastion server PostgreSQL client version and RDS PostgreSQL engine major version

Update the PostgreSQL client package version in `ops/infrastructure/bastion_playbook.yml`, for example:
```
    - name: Install PostgreSQL 14 client packages
      become: yes
      dnf:
        name: postgresql14
        state: present

    - name: Test pg_isready can connect to RDS instance
      ansible.builtin.command: "/usr/pgsql-14/bin/pg_isready -h {{ pg_host }}"
      register: pg_isready
    - debug: msg="{{ pg_isready.stdout }}"
```

Here is the [official document](https://docs.aws.amazon.com/AmazonRDS/latest/PostgreSQLReleaseNotes/postgresql-release-calendar.html) for supported PostgreSQL version on AWS RDS, it contains the information about the engine version and its end of standard support date.
Update the RDS instance configuratin in `ops/infrastructure/modules/rds-instance/rds-instance.tf`, for example:
```
engine_version            = "14.8"
family                    = "postgres14"  # DB parameter group
major_engine_version      = "14"          # DB option group
```

##### Update Yii framework version

In `.gitlab-ci.yml`, update the value of the variables `YII_VERSION` and `YII2_VERSION`, for example:
```
YII_VERSION: "1.1.28"
YII2_VERSION: "2.0.48"
```

##### Steps to check for the version updates in developer's environments

###### Staging
```
# instantiate and provision aws EC2 servers freshly after checkout this branch
% cd /gigadb-website/ops/infrastructure/envs/staging
%  ../../../scripts/tf_init.sh --project gigascience/forks/kencho-gigadb-website --env staging
% terraform apply
% terraform refresh
# go to aws RDS console, click $database, Configuration, check for the engine version
% ../../../scripts/ansible_init.sh --env staging
% TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories webapp_playbook.yml -v
% ansible-playbook -i ../../inventories bastion_playbook.yml -e "backupDate=latest" -v
# make sure staging has been deployed successfully through the gitlab pipeline, eg. sd_gigadb
# staging now should have the latest datasets, check the RSS feed in staging website
# log in to the staging bastion
% ssh -i "$aws.pem" centos@"$basion.ip"
[centos@ip-10-xx-x-xx ~]$ pg_dump --version
pg_dump (PostgreSQL) 14.8
[centos@ip-10-xx-x-xx ~]$ psql --version
psql (PostgreSQL) 14.8
# upload the current latest database to s3
[centos@ip-10-xx-x-xx ~]$ docker run --env-file .env -v /home/centos/backups:/backups -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_s3backup:staging 2> logs/upload-errors-"$latestDate".log 1> logs/upload-output-"$latestDate".log
# log in aws console s3 dashboard to check s3:gigadb-database-backups bucket and look for gigadb_gigascience-forks-$GITLAB_USERNAME-gigadb-website_$env_"$latestDate".backup, eg: gigadb_gigascience-forks-kencho-gigadb-website_staging_20230620.backup
# back to the bastion server
# download and restore the datasets from the earlier date
[centos@ip-10-xx-x-xx ~]$ ./databaseReset.sh 20230606 2> logs/errors-6jun.log 1> logs/output-6jun.log
# check the RSS feed in the staging website
# restore the $latest backup from s3
[centos@ip-10-xx-x-xx ~]$ docker run --rm --env-file .env -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf --entrypoint /restore_database_from_s3_backup.sh registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_s3backup:staging "$latestDate" 2> logs/restore-latest-erros.log 1> logs/restore-latest-output.log
# check the RSS feed in staging website
# check the logs/*output* logs, the migration jobs are performed by the following tools
Yii Migration Tool v1.0 (based on Yii v1.1.28)
Yii Migration Tool (based on Yii v2.0.48)
```

###### Live
```
# instantiate and provision aws EC2 servers freshly after checkout this branch
% cd /gigadb-website/ops/infrastructure/envs/live
%  ../../../scripts/tf_init.sh --project gigascience/forks/kencho-gigadb-website --env live
% terraform apply
% terraform refresh
# go to aws RDS console, click $database, Configuration, check for the engine version
% ../../../scripts/ansible_init.sh --env live
% TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories webapp_playbook.yml -v
% ansible-playbook -i ../../inventories bastion_playbook.yml -e "backupDate=latest" -v
# make sure live has been deployed successfully through the gitlab pipeline, eg. ld_gigadb
# live now should have the latest datasets, check the RSS feed in staging website
# log in to the live bastion
% ssh -i "$aws.pem" centos@"$basion.ip"
[centos@ip-10-xx-x-xx ~]$ pg_dump --version
pg_dump (PostgreSQL) 14.8
[centos@ip-10-xx-x-xx ~]$ psql --version
psql (PostgreSQL) 14.8
# upload the current latest database to s3
[centos@ip-10-xx-x-xx ~]$ docker run --env-file .env -v /home/centos/backups:/backups -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_s3backup:live 2> logs/upload-errors-"$latestDate".log 1> logs/upload-output-"$latestDate".log
# log in aws console s3 dashboard to check s3:gigadb-database-backups bucket and look for gigadb_gigascience-forks-$GITLAB_USERNAME-gigadb-website_$env_"$latestDate".backup, eg: gigadb_gigascience-forks-kencho-gigadb-website_live_20230620.backup
# back to the bastion server
# download and restore the datasets from the earlier date
[centos@ip-10-xx-x-xx ~]$ ./databaseReset.sh 20230606 2> logs/errors-6jun.log 1> logs/output-6jun.log
# check the RSS feed in staging website
# restore the $latest backup from s3
[centos@ip-10-xx-x-xx ~]$ docker run --rm --env-file .env -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf --entrypoint /restore_database_from_s3_backup.sh registry.gitlab.com/gigascience/forks/kencho-gigadb-website/production_s3backup:live "$latestDate" 2> logs/restore-latest-erros.log 1> logs/restore-latest-output.log
# check the RSS feed in the live gigadb website
# check the logs/*output* logs, the migration jobs are performed by the following tools
Yii Migration Tool v1.0 (based on Yii v1.1.28)
Yii Migration Tool (based on Yii v2.0.48)
```

##### Steps to check for the version updates in Upstream live

###### Upstream live (only if all steps are passing in developer's environments)
```
# log in upstream accrount 
# instantiate and provision aws EC2 servers freshly after checkout this branch
% cd /gigadb-website/ops/infrastructure/envs/live
% ../../../scripts/tf_init.sh --project gigascience/upstream/gigadb-website --env live
% terraform apply
% terraform refresh
# go to aws RDS console, click $database, Configuration, check for the engine version
% ../../../scripts/ansible_init.sh --env live
% TF_KEY_NAME=private_ip ansible-playbook -i ../../inventories webapp_playbook.yml -v
% ansible-playbook -i ../../inventories bastion_playbook.yml -e "backupDate=latest" -v
# make sure upstream live has been re-builded and re-deployed successfully through the gitlab pipeline from the `upstream/gigadb-website/`, eg. build_live, ld_gigadb
# upstream live now should have the latest datasets, check the RSS feed in the live gigadb website, https://beta.gigadb.org
# log in to the upstream live bastion server
% ssh -i "$aws-upstream.pem" centos@"$basion.ip"
[centos@ip-10-xx-x-xx ~]$ pg_dump --version
pg_dump (PostgreSQL) 14.8
[centos@ip-10-xx-x-xx ~]$ psql --version
psql (PostgreSQL) 14.8
# upload the current latest database to s3
[centos@ip-10-xx-x-xx ~]$ docker run --env-file .env -v /home/centos/backups:/backups -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf registry.gitlab.com/gigascience/upstream/gigadb-website/production_s3backup:live 2> logs/upload-errors-"$latestDate".log 1> logs/upload-output-"$latestDate".log
# log in aws console s3 dashboard to check s3:gigadb-database-backups bucket and look for gigadb_gigascience-upstream-gigadb-website_live_"$latestDate".backup, eg: gigadb_gigascience-upstream-gigadb-website_live_20230628.backup
# back to the upstream live bastion server
# download and restore the datasets from the earlier date
[centos@ip-10-xx-x-xx ~]$ ./databaseReset.sh 20230606 2> logs/errors-6jun.log 1> logs/output-6jun.log
# check the RSS feed in the live gigadb website, https://beta.gigadb.org
# restore the $latest backup from s3
[centos@ip-10-xx-x-xx ~]$ docker run --rm --env-file .env -v /home/centos/.config/rclone/rclone.conf:/root/.config/rclone/rclone.conf --entrypoint /restore_database_from_s3_backup.sh registry.gitlab.com/gigascience/upstream/gigadb-website/production_s3backup:live "$latestDate" 2> logs/restore-latest-erros.log 1> logs/restore-latest-output.log
# check the RSS feed in the live gigadb website, https://beta.gigadb.org
# check the logs/*output* logs, the migration jobs are performed by the following tools
Yii Migration Tool v1.0 (based on Yii v1.1.28)
Yii Migration Tool (based on Yii v2.0.48)
```