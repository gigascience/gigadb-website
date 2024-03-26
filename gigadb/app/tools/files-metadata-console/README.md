# Files Metadata Console

## Updating dataset file URLs with Wasabi prefix

### Preparation

Go to `gigadb/app/tools/files-metadata-console` and create a .env file: 
```
$ docker-compose run --rm configure
```

Update the .env file with values for REPO_NAME and GITLAB_PRIVATE_TOKEN. Re-run
`docker-compose run --rm configure` to create a .secrets file and other 
configuration files in config directory.

### Dev environment

To begin update of file URLs in batches of 3 datasets, execute in dry run mode:
```
$ docker-compose run --rm files-metadata-console ./yii update/urls --separator=/pub/ --exclude='100020' --next=3
```
> Dataset 100020 has been excluded so is not processed by the tool.

To make changes to the database, use the `--apply` flag:
```
$ docker-compose run --rm files-metadata-console ./yii update/urls --separator=/pub/ --exclude='100020' --next=3 --apply
```

You can also specify a stop DOI so when the current DOI to be processed is
greater than or equal to the stop DOI then File URL transformation is
terminated:
```
$ docker-compose run --rm files-metadata-console ./yii update/urls --separator=/pub/ --exclude='100020' --next=3 --stop='200000' --apply
```

### Test with production data

```
# Connect to dev database
$ PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb postgres
# Delete connections on a given database
postgres=# SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'gigadb';
# Create new empty database
postgres=# drop database gigadb;
postgres=# create database owner gigadb;
postgres=# \q
# Load database backup file
$ docker-compose run --rm test pg_restore -h database -p 5432 -U gigadb -d gigadb -v "/gigadb/app/tools/files-metadata-console/sql/gigadbv3_20230622.backup"
# Run tool
$ docker-compose run --rm files-metadata-console ./yii update/urls --separator=/pub/ --exclude='100396,100446,100584,100747,100957,102311' --stop=200002 --next=3 --apply 
```

#### Dataset 100396

Dataset 100396 contains nearly 200,000 files which all need to have their 
location column updating with correct Wasabi URL. The tool is not able to work 
with so many files and so need to update manually. Firstly, update ftp_site
column in dataset table for Dataset 100396:
```
# Connect to dev database
$ PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb postgres
# Delete connections on a given database
postgres=# SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'gigadb';
postgres=# \q
# Load database backup file
$ docker-compose run --rm test pg_restore -h database -p 5432 -U gigadb -d gigadb -v "/gigadb/app/tools/files-metadata-console/sql/gigadbv3_20230619.backup"
# Connect to dev database
$ PGPASSWORD=vagrant psql -h localhost -p 54321 -U gigadb postgres
# Delete connections on a given database
postgres=# SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'gigadb';
postgres=# \c gigadb;
# Update ftp_site column for dataset
gigadb=# Update dataset set ftp_site = REPLACE(ftp_site, 'https://ftp.cngb.org/pub/gigadb/pub/', 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/') where identifier = '100396';
UPDATE 1
# Test updates have been made
gigadb=# select ftp_site from dataset where identifier = '100396';
                                            ftp_site                                            
------------------------------------------------------------------------------------------------
 https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100396/
(1 row)
```

Next, update all location fields for all files associated with dataset 100396:
```
# Update location column for dataset files - will take about a minute to complete
gigadb=# Update file set location = REPLACE(location, 'https://ftp.cngb.org/pub/gigadb/pub/', 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/') where dataset_id = 629;
UPDATE 197214
# Test updates have been made
gigadb=# select * from file where dataset_id = 629 and id = 287662;
 287662 |        629 | 50930810.pdb | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100396/eModelBDB/810/50930810.pdb | pdb       | 370380 | reactant set ID 50930810 | 2018-07-04 |         6 |     254 | FILE_CODE |             |              0 | 
```

### Staging and live environments

To complete a thorough transformation of all dataset file location URLs, the 
values of specific command-line parameters should be determined.

1. Identify the DOI of the latest dataset whose data has been migrated to Wasabi.
For example on Tue 26 Sep 2023, it was dataset 102456 for http://gigadb.org. The
value for --stop parameter should therefore be 102457.
2. Determine the list of DOIs that should be excluded from File location URL
updated by consulting the [big dataset spreadsheet](https://docs.google.com/spreadsheets/d/13DVSESKMTewlv11Z7cVNPEBMo4lzQa4X_dB0OFc5N_Y/edit#gid=0).

Drop database triggers otherwise tool will hang due to memory issues:
```
# Log into bastion server using SSH
$ ssh -i ~/.ssh/id-rsa-aws-tokyo-peter.pem centos@bastion-ip 
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'drop trigger if exists file_finder_trigger on file RESTRICT'
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'drop trigger if exists sample_finder_trigger on sample RESTRICT'
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'drop trigger if exists dataset_finder_trigger on dataset RESTRICT'
```

Execute tool (in batches of 50 datasets) in dry run mode to check the
functionality is working:
```
$ docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/urls --separator=/pub/ --stop=102457 --next=50 --exclude='100050,100115,100157,100242,100310,100396,100443,100608,100622,100707,100849,102425,102431,102440,102441'
```

Execute tool until all datasets have had their file locations updated with 
Wasabi links:
```
$ docker run --rm "registry.gitlab.com/$GITLAB_PROJECT/production-files-metadata-console:$GIGADB_ENV" ./yii update/urls --separator=/pub/ --stop=102457 --next=50 --exclude='100050,100115,100157,100242,100310,100396,100443,100608,100622,100707,100849,102425,102431,102440,102441' --apply
```

> The dataset file update process should take up to 1 hour and a half to 
> transform production GigaDB data from start to finish on a bastion server.

Re-create database triggers:
```
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'create trigger file_finder_trigger after insert or update or delete or truncate on file for each statement execute procedure refresh_file_finder()'
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'create trigger sample_finder_trigger after insert or update or delete or truncate on sample for each statement execute procedure refresh_sample_finder()'
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c 'create trigger dataset_finder_trigger after insert or update or delete or truncate on dataset for each statement execute procedure refresh_dataset_finder()'
```

The information on dataset page displayed to users may not reflect the changes 
to dataset FTP site and file location URLs now in the database because of how
the current GigaDB website caching functionality works. For this reason, the
web application should be restarted to reset the cache on the Gitlab pipeline
web console. This can be done on the Pipelines page on the Gitlab web console by
manually executing `*_stop_app` and `*_start_app` jobs. You should now be able
to go to any GigaDB website page for a dataset that is not in the excluded list
and see file links that point to Wasabi.

Some datasets have been excluded from having their File location URLs updated
because they contain too many files for the files metadata console tool to 
process. These datasets need to be manually updated using SQL commands. We begin
by starting a database transaction so that each manual dataset update is 
contained within a unit of work:
```
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c "BEGIN TRANSACTION;"
```

You can identify these datasets using the big datasets spreadsheet as 
they will need to be manually updated. You can confirm these datasets have not 
been updated by cross-referencing them with the DOIs listed by this SQL command:
```
# Identify datasets whose ftp_site URLs contain 'cngb'
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c "SELECT id, identifier, ftp_site FROM dataset WHERE ftp_site ~ 'cngb' ORDER BY identifier ASC;"
  id  | identifier |                             ftp_site                              
------+------------+-------------------------------------------------------------------
   xx | 100050     | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100050/
  xxx | 100115     | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100115
  xxx | 100157     | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100157/
  xxx | 100310     | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100310/
```

Manually update `ftp_site` field in dataset table for those datasets:
```
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c "Update dataset set ftp_site = REPLACE(ftp_site, 'https://ftp.cngb.org/pub/gigadb/pub/', 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/') where identifier = '100310';"
UPDATE 1
```

Check result of above command:
```
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c "select id, identifier, ftp_site from dataset where identifier = '100310';"
 id  | identifier |                                            ftp_site                                            
-----+------------+------------------------------------------------------------------------------------------------
 xxx | 100310     | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100310/
(1 row)
```

Finally, manually update file location URLs for those datasets containing too 
many files which will result in a memory error, e.g. dataset 100310.
```
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c "Update file set location = REPLACE(location, 'https://ftp.cngb.org/pub/gigadb/pub/', 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/') where dataset_id = 385;"
UPDATE 19981
```

Check result of above command:
```
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c "select location from file where dataset_id = '385' limit 5;"
                                                                                             location                                                                                             
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100310/mapDamage/14Oral_mapDamage_results/mapDamage_Oral_006/Stats_out_MCMC_iter_summ_stat.csv
 https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100310/mapDamage/Human_Actinobacteria_mapDamage_results/mapDamage_Actino_oral_156/Stats_out_MCMC_iter.csv
 https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100310/mapDamage/14Oral_mapDamage_results/mapDamage_Oral_041/Stats_out_MCMC_iter_summ_stat.csv
 https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100310/mapDamage/14Oral_mapDamage_results/mapDamage_Oral_043/dnacomp_genome.csv
 https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100310/mapDamage/14Oral_mapDamage_results/mapDamage_Oral_042/Stats_out_MCMC_iter_summ_stat.csv
(5 rows)
```

Finally, we commit the dataset changes and end the database transaction:
```
$ docker run --rm  --env-file ./db-env registry.gitlab.com/$GITLAB_PROJECT/production_pgclient:$GIGADB_ENV -c "COMMIT TRANSACTION;"
```

## Running unit and functional tests in files metadata console tool

Execute all unit tests:
```
$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/unit
```

Execute single unit test:
```
$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/unit/DatasetFilesURLUpdaterTest.php:^testGetPendingDatasets$
```

Execute all functional tests:
```
$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/functional
```

Execute single functional test:
```
$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/functional/CheckValidURLsCest.php:^tryNoIssueToReport$

$ docker-compose run --rm files-metadata-console ./vendor/codeception/codeception/codecept run --debug tests/functional/ReplaceFileUrlSubstringWithPrefixCest:^tryExcludingDOIsFromFileUrlChanges$
```
