# Tools for GigaDB: files-url-updater


## Start the local test database

```
$ docker-compose up -d pg9_3
```
## Run test

```
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
$ docker-compose run --rm updater ./yii dataset-files/download-restore-backup 20210530
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