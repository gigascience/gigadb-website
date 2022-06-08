# Giga Review

## Getting started

Ensure the main app is running (as we need beanstalkd service to be running)

```
$ ./up.sh

```

Then start the gigareview application
```
$ cd gigareview
$ ./up.sh
```

## How was the project bootstrapped (for info only)

1. Create project structure
```
$ docker-compose run --rm test composer create-project --prefer-dist yiisoft/yii2-app-advanced gigareview
```

2. Update ``docker-compose.yml``

3. Update ``Dockerfile`` for each module

4. create ``env-sample``

5. Ensure canonical location for configuration files remains the ``environments`` directory

6. Ensure shared configuration is created in the ``common`` sub-directory of the above directory

7. Update ``.gitignore`` to reflect the configuration strategy

## how were the tables created

```
$ docker-compose run --rm console ./yii migrate/create create_ingest_table --fields="file_name:string,report_type:integer,fetch_status:integer,parse_status:integer,store_status:integer,remote_file_status:integer,created_at:datetime, updated_at:datetime"
```