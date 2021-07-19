# Tools for GigaDB: dataset-backup-tool

## Set up

To run the Tencent backup smoke tests, the tests need access to a Tencent Cloud
account which is provided by these variables: `TENCENTCLOUD_SECRET_ID`,
`TENCENTCLOUD_SECRET_KEY`, `TENCENTCLOUD_APP_ID`. These variables need to be 
added as new GitLab secrets. 

Running `docker-compose run --rm config` will then pull the new variables
into the `.secrets` file and create a configuration file `.cos.conf` and shell 
scripts, `create_bucket.sh` and `delete_bucket.sh` in the 
`dataset-backup-tool/scripts` directory. The smoke tests uses these shell 
scripts for creating and deleting a Tencent bucket at the start and end of the
tests. The `.cos.conf` file provides configuration for running `coscmd` commands
in `BackupSmokeCest` functional test class.

## Run Tencent backup smoke tests

There are 3 smoke tests in `tests/functional/BackupSmokeCest` which backup data
files to a `dataset` directory in a Tencent bucket:
* `tryBackupDataset` will upload 3 files from the `tests/_data/dataset1` 
  directory into the `dataset` directory in a Tencent bucket.
* `tryUpdateBackupWithChangedFile` checks that the `coscmd` tool can detect 
  differences between files. This test should only upload `test.csv` from
  `tests/_data/dataset2` into the Tencent `dataset` backup directory since only 
  this csv file is different in the `dataset2` directory compared to the 
  `dataset1` directory.
* `tryUpdateBackupWithDeletedFile` checks that the `--delete` parameter is able
  to synchronise the contents of a source directory with its counterpart 
  directory in a Tencent bucket. The `tests/_data/dataset3` directory is missing
  `test.tsv` so this test checks that the `test.tsv` file in the Tencent bucket 
  `dataset` directory has been deleted.

To run these smoke tests:
```
# Change directory to dataset-backup-tool directory
$ cd gigadb/app/tools/dataset-backup-tool
# Run tests
$ docker-compose run --rm backup_tool ./vendor/bin/codecept run tests/functional/BackupSmokeCest.php
```


