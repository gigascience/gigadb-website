# Tools for GigaDB: dataset-backup-tool

Move to the tool directory before entering any of the commands in this document
```
$ cd gigadb/app/tools/dataset-backup-tool
```

## Run controller

```
$ docker-compose run --rm backup_tool ./yii dataset-files/upload-files-to-bucket
```

## Run test

```
$ docker-compose run --rm updater ./scripts/webapp_setup.sh
$ docker-compose run --rm updater ./vendor/bin/codecept build
$ docker-compose run --rm updater ./vendor/bin/codecept run -v

```

## run specific tests

```
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/functional/DatasetFilesCest.php
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/functional -v
$ docker-compose run --rm updater ./vendor/bin/codecept run tests/acceptance -v
```


## Run commands

```
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --date 20210530
$ docker-compose run --rm updater ./yii dataset-files/update-ftp-url --ids 12,34,67
```

## Get help

```
$ docker-compose run --rm updater ./yii
$ docker-compose run --rm updater ./yii help dataset-files
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup --help

```

## Adding new acceptance tests

1. add ``*.feature`` files to ``tests/acceptance``
2. Run:
```
  $ docker-compose run --rm updater ./vendor/bin/codecept gherkin:snippets acceptance
```
3. Implement steps in ``tests/_support/AcceptanceTester.php``
