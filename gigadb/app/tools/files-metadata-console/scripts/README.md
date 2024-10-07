# Scripts

## md5.sh - Calculation of md5 checksum values

### Dev environment

From a curator's perspective, the md5.sh script is meant to be executed from
within a user dropbox directory.
```
$ pwd
path/to/gigadb/app/tools/files-metadata-console/tests/_data/dropbox/user4
# Run tool
$ ../../../../scripts/md5.sh 102480
Created 102480.md5
Created 102480.filesizes
```

If you list the contents in `tests/_data/user4`, you will see two new metadata
files have been created:
```
# Check metadata files have been created
$ ls -lh
-rw-r--r--  1 peterli  staff    68B Oct  3 19:07 102480.filesizes
-rw-r--r--  1 peterli  staff   118B Oct  3 19:07 102480.md5
drwxr-xr-x  3 peterli  staff    96B Oct  3 09:33 analysis_data
-rw-r--r--  1 peterli  staff   3.1K Oct  3 15:38 readme_102480.txt
```

There is also a bats test script md5.sh:
```
$ pwd
path/to/gigadb/app/tools/files-metadata-console
$ bats tests/bats/md5.bats
 ✓ No DOI provided
 ✓ DOI provided
 ✓ Confirm md5 and file size values
 ✓ Execute md5.sh within container

4 tests, 0 failures
```

## filesMetaToDb.sh - update file sizes and md5 values for files in database

### Dev environment

Like md5.sh, the filesMetaToDb.sh script is designed for curators to use from
within a user dropbox directory:
```
# Change directory
$ cd gigadb/app/tools/files-metadata-console/tests/_data/dropbox/user2
# Run script
$ ../../../../scripts/filesMetaToDb.sh 100039
Updating md5 checksum values as file attributes for 100039
Number of changes: 3
Updating file sizes for 100039
Number of changes: 3
Updated file metadata for 100039 in database
```

## postUpload.sh - Perform post upload operations to create readme file and update file metadata in database

### Dev environment

Like md5.sh and filesMetaToDb.sh, the postUpload.sh script is designed for
curators to use from within a user dropbox directory:
```
# Change directory to a test user dropbox
$ cd gigadb/app/tools/files-metadata-console/tests/_data/dropbox/user2
# Run script
$ ../../../../scripts/postUpload.sh --doi 100039 --dropbox user2
Creating README file for 100039
Creating dataset metadata files for 100039
Created 100039.md5
Created 100039.filesizes
Updating file sizes and MD5 values in database for 100039
Updating md5 checksum values as file attributes for 100039
Number of changes: 4
Updating file sizes for 100039
Number of changes: 4
Updated file metadata for 100039 in database
```

## Transformation of dataset ftp_site and file location URLs

The `updateUrl.sh` bash script in this directory can be used to transform the
URLs in dataset ftp_site and file location table columns in to Wasabi links.

### Dev environment

Go to `gigadb/app/tools/files-metadata-console` and create a .env file:
```
$ docker-compose run --rm configure
```

Update the .env file with values for REPO_NAME and GITLAB_PRIVATE_TOKEN. Re-run
`docker-compose run --rm configure` to create a .secrets file and other
configuration files in config directory.

To use `updateUrl.sh` in your dev environment, it requires a Postgresql database
containing GigaDB data which can be deployed by running:
```
# Up your GigaDB dev environment
$ pwd
/path/to/gigadb-website
$ ./up.sh
```

Look at the ftp_site column in dataset table. It will contain a mix of old ftp 
URLs and https://ftp.cngb.org links:
```
$ docker-compose run --rm test bash -c "PGPASSWORD=vagrant psql -h database -p 5432 -U gigadb -d gigadb -c 'select ftp_site from dataset;'"
```

Also, look at location column in file table. It will contain a mix of old climb 
ftp URLs and https://ftp.cngb.org links:
```
$ docker-compose run --rm test bash -c "PGPASSWORD=vagrant psql -h database -p 5432 -U gigadb -d gigadb -c 'select location from file;'"
```

Go to the `files-metadata-console` directory and create it's configuration:
```
$ cd gigadb/app/tools/files-metadata-console
# Create .env file
$ ./configure
# Add your Gitlab project name and private token into .env file
# Execute configure again to create .secrets file
$ ./configure
# Install tool dependencies
$ docker-compose run --rm composer composer install
```

Execute updateUrls.sh script
```
$ scripts/updateUrls.sh
Processing dataset table
SELECT 10
Created dataset_changes temporary table
UPDATE 10
UPDATE 10
UPDATE 10
Transformed URLs in ftp_site column in temp table
UPDATE 10
Copied URLs from temporary table into dataset table ftp_site column
SET
DO
Asserted that all rows in temporary table were copied into dataset table
Processing file table
SELECT 70
Created dataset_changes temporary table
UPDATE 70
UPDATE 70
UPDATE 70
Transformed URLs in location column in temp table
UPDATE 70
Copied URLs from temporary table into dataset table ftp_site column
SET
DO
Asserted that all rows in temporary table were copied into file table
COMMIT
All SQL commands were successfully committed in database transaction!
```

If you now look in the dataset and file tables, it will now contain Wasabi URLs
that begin with `https://s3.ap-northeast-1.wasabisys.com`:
```
$ docker-compose run --rm test bash -c "PGPASSWORD=vagrant psql -h database -p 5432 -U gigadb -d gigadb -c 'select ftp_site from dataset;'"
$ docker-compose run --rm test bash -c "PGPASSWORD=vagrant psql -h database -p 5432 -U gigadb -d gigadb -c 'select location from file;'"
```

Re-running `scripts/updateUrls.sh` will result in the script displaying an
error because URLs in dataset and file tables already contain wasabi links so 
the assertions in `updateUrls.sh` will not pass:
```
$ scripts/updateUrls.sh
Processing dataset table...
SELECT 0
Created dataset_changes temporary table
UPDATE 0
UPDATE 0
UPDATE 0
Transformed URLs in ftp_site column in temp table
UPDATE 0
Copied URLs from temporary table into dataset table ftp_site column
SET
ERROR:  No. of row changes in dataset table does not equal no. of rows in dataset_changes table!
CONTEXT:  PL/pgSQL function inline_code_block line 9 at ASSERT
```

#### Testing updateUrls.sh using bats-core test

A bats-core test in tests/updateUrls.bats can be used to test updateUrls.sh:
```
$ bats tests
✓ transform dataset and file URLs

1 test, 0 failures
```

### Staging environment

Instantiate the AWS resources for your staging environment:
```
$ cd /path/to/gigadb-website/ops/infrastructure/envs/staging

# Copy terraform files to staging environment
$ ../../../scripts/tf_init.sh --project gigascience/forks/your-gigadb-website --env staging

# Provision with Terraform
$ terraform plan
$ terraform apply
$ terraform refresh

# Copy ansible files
$ ../../../scripts/ansible_init.sh --env staging

# Provision bastion ec2 server and restore RDS with latest database backup
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --extra-vars="gigadb_env=staging"

# Provision webapp ec2 server with ansible
$ TF_KEY_NAME=private_ip env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories webapp_playbook.yml --extra-vars="gigadb_env=staging"
```

Run Gitlab CI/CD pipeline

SSH into your bastion server:
```
$ ssh -i "~/.ssh/your-id-rsa-aws.pem" centos@ec2_bastion_public_ip
```

Check dataset table ftp_site and file location column contents:
```
[centos@ip-10-99-0-163 ~]$ source .env
# Contents of ftp_site in dataset table should be CNGB links
[centos@ip-10-99-0-163 ~]$ PGPASSWORD=$PGPASSWORD psql -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c 'select identifier, ftp_site from dataset ORDER BY identifier ASC;'
# Contents of location in file table should be CNGB links
[centos@ip-10-99-0-163 ~]$ PGPASSWORD=$PGPASSWORD psql -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c 'select id, dataset_id, location from file;'
```

Execute updateUrls.sh script - it should take 2-3 minutes to run when applied on
production data:
```
[centos@ip-10-99-0-163 ~]$ ./updateUrls.sh
Processing dataset table...
SELECT 2444
Created dataset_changes temporary table
UPDATE 2444
UPDATE 2444
UPDATE 2444
Transformed URLs in ftp_site column in temp table
UPDATE 2444
Copied URLs from temporary table into dataset table ftp_site column
SET
DO
Asserted that all rows in temporary table were copied into dataset table
Processing file table...
SELECT 351716
Created dataset_changes temporary table
UPDATE 351716
UPDATE 351716
UPDATE 351716
Transformed URLs in location column in temp table
UPDATE 351716
Copied URLs from temporary table into dataset table ftp_site column
SET
DO
Asserted that all rows in temporary table were copied into file table
COMMIT
All SQL commands were successfully committed in database transaction!
```

Check dataset and file table contents again to see if they now contain Wasabi
links:
```
[centos@ip-10-99-0-163 ~]$ source .env
[centos@ip-10-99-0-163 ~]$ PGPASSWORD=$PGPASSWORD psql -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c 'select identifier, ftp_site from dataset ORDER BY identifier ASC;'
[centos@ip-10-99-0-163 ~]$ PGPASSWORD=$PGPASSWORD psql -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -c 'select id, dataset_id, location from file ORDER BY id ASC;'
```

Go to `/dataset/102478` on your staging server in a browser and check the URL for
its files which should be Wasabi links. If not, run the `sd_stop_app` and
`sd_start_app` jobs on your CI/CD pipeline.