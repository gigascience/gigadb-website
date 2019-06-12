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
```
