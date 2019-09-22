# Developer Guide for File Upload Wizard


## Running tests

### running all (unit and fonctional) tests for all tiers (backend and frontend)

```
$ docker exec console bash
# cd /app
# php yii_test migrate
# vendor/bin/codecept build
# vendor/bin/codecept run
```

and with coverage:

```
$ docker exec console bash
# cd /app
# vendor/bin/codecept run --coverage --coverage-xml --coverage-html
```

### running all tests from outside the container

```
$ docker-compose exec console /app/vendor/bin/codecept run -c /app --coverage
```

### running all tests for specific tier

```
$ docker exec console bash
# cd /app/backend
# ../vendor/bin/codecept build
# ../vendor/bin/codecept run
```

### running specific test suites

```
$ docker exec console bash
# cd /app/backend
# ../vendor/bin/codecept run unit
```

### running test coverage for unit and functional tests


```
$ docker exec console bash
# cd /app/backend
# ../vendor/bin/codecept run --coverage --coverage-xml --coverage-html
```

### running all tests for GigaDB and File Upload Wizard

If GigaDB and File Upload wizard webapps are all running, you can run test suite across them all:

```
$ ./tests/unit_runner
$ ./tests/functional_runner
$ ./tests/all_and_coverage
```

## Create a new model (replace backend with common or frontend if needed)

```
$ docker exec console bash
# cd /app
# php yii gii/model --tableName filedrop_account --modelClass FiledropAccount --ns 'backend\models'
```

## Create a unit test (replace backend with common or frontend if needed)

```
# vendor/bin/codecept generate:test unit FiledropAccountTest -- -c backend
# vendor/bin/codecept build
```
## Create a functional test

```
# vendor/bin/codecept generate:cest functional FiledropAccountCest -- -c backend
# vendor/bin/codecept build
```

## Create a new controller (for the backend app) with three actions

```
$ docker exec console bash
# cd /app
# php yii gii/controller --controllerClass="backend\controllers\FiledropAccountController" --actions=create,close,index --viewPath="backend/views/filedrop-account"
```
## query the REST API

```
$ curl -sSL -D - -o /dev/null -i -H "Accept:application/json" -H "Content-Type:application/json" -XPOST "http://fuw-admin-dev.pommetab.com:7070/filedrop-accounts" -d '{"doi": "example", "email": "user@example.com"}'
```

**Note:**
> ControllerIDs are pluralized by default, so _FiledropAccountController_ will have ``filedrop-accounts`` as controllerID by default.
> This make sense when seeing REST endpoints as resources to act upon.
> it can be changed by reconfiguring 'urlManager' in main.php.

## Accessing the docker daemon from container

if using a mac, first install socat using brew:
```
$ brew install socat
```
then do:
```
$ socat TCP-LISTEN:2375,reuseaddr,fork UNIX-CONNECT:/var/run/docker.sock &
```

because Docker for Mac doesn't allow  the daemon on a TCP port. The above steps are not necessary on Windows or Linux.

Using a php dev container (test or console services), one can then use:

```
$ docker-compose exec console bash
# echo -e "GET /info HTTP/1.0\r\n" | nc -v host.docker.internal 2375 | awk 'NR==1,/^\r$/ {next} {printf "%s%s",$0,RT}' | jq
```

For security, do not mount directly the Docker unix socket in any container. TCP socket access is the safe method.

## Accessing logs for Docker Daemon

```
~/Library/Containers/com.docker.docker/Data/log/host/com.docker.driver.amd64-linux.log
```

## Working with database schema on Yii2 application (File Upload Wizard)

Use Yii2 migrations to describe new changes to the database schema.

```
$ docker exec console bash
# cd /app
# ./yii migrate/create create_upload_table
```

To run migrations (it will only run the ones not already applied):

```
$ docker-compose exec console /app/yii migrate --interactive=0
```

To revert the latest migration:
```
$ docker-compose exec console /app/yii migrate/down --interactive=0
```

**Note: **[More info on Yii2 migrations](https://www.yiiframework.com/doc/guide/2.0/en/db-migrations)


## Services

### ftpd

#### account databases
```
root@5e7517fa37ef:/# ls -alrt /etc/pure-ftpd/passwd/pureftpd.passwd
-rw------- 1 root root 171 Jun 21 13:53 /etc/pure-ftpd/passwd/pureftpd.passwd
root@5e7517fa37ef:/# ls -alrt /etc/pure-ftpd/pureftpd.pdb
-rw------- 1 root root 2237 Jun 21 13:55 /etc/pure-ftpd/pureftpd.pdb
```

#### debugging

view list of accounts:
```
$ docker-compose exec ftpd cat /etc/pure-ftpd/passwd/pureftpd.passwd
```
view ftp account detail for user **uploader-100004**:
```
$ docker-compose exec ftpd pure-pw show uploader-100004 -f /etc/pure-ftpd/passwd/pureftpd.passwd
```


Debugging the connection using ``ncftp``

```
$ ncftpput -u uploader-100004 -P 9021 -p QEEDE8VBkHSNhDx1 -d ~/debugging.log fuw.rija.dev /  ~/Downloads/Before_FIAR_Animal_Classification.pdf
```

``~/debugging.log`` will contain the low-level connection detail similar to:
```
2019-09-17 20:47:05  LibNcFTP 3.2.6 (November 12, 2016) compiled for macosx10.13
2019-09-17 20:47:05  Uname: Darwin|Faith.local|18.7.0|Darwin Kernel Version 18.7.0: Tue Aug 20 16:57:14 PDT 2019; root:xnu-4903.271.2~2/RELEASE_X86_64|x86_64
2019-09-17 20:47:05  Sysinfo: Mac OS X 10.14.6 (Build 18G95)
2019-09-17 20:47:07  220: --------- Welcome to Pure-FTPd [privsep] [TLS] ----------
2019-09-17 20:47:07       You are user number 1 of 5 allowed.
2019-09-17 20:47:07       Local time is now 18:47. Server port: 21.
2019-09-17 20:47:07       This is a private system - No anonymous login
2019-09-17 20:47:07       IPv6 connections are also welcome on this server.
2019-09-17 20:47:07       You will be disconnected after 15 minutes of inactivity.
2019-09-17 20:47:07  Connected to fuw.rija.dev.
2019-09-17 20:47:07  Cmd: USER uploader-100004
2019-09-17 20:47:07  331: User uploader-100004 OK. Password required
2019-09-17 20:47:07  Cmd: PASS xxxxxxxx
2019-09-17 20:47:08  Remote host has closed the connection.
2019-09-17 20:47:08  421: Home directory not available - aborting
2019-09-17 20:47:08  Sleeping 18 seconds.
2019-09-17 20:47:26  Retry Number: 1

```