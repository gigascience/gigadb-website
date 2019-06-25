# Developer Guide for File Upload Wizard


## Running tests

```
$ docker exec console bash
# cd /app
# php yii_test migrate
# vendor/bin/codecept build
# vendor/bin/codecept run
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

## Services

### ftpd

```
root@5e7517fa37ef:/# ls -alrt /etc/pure-ftpd/passwd/pureftpd.passwd
-rw------- 1 root root 171 Jun 21 13:53 /etc/pure-ftpd/passwd/pureftpd.passwd
root@5e7517fa37ef:/# ls -alrt /etc/pure-ftpd/pureftpd.pdb
-rw------- 1 root root 2237 Jun 21 13:55 /etc/pure-ftpd/pureftpd.pdb
```