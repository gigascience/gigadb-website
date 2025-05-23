# DATACITE API


## Check for missing DOIs on the DATACITE API

### Dev environment

#### Check if DOI exists in batch mode

```
$ docker-compose run --rm application ./protected/yiic checkdoiexistsindataciteapi
Processing batch of 10 datasets with offset 0
[OK] DOI exists for dataset 100020
[ERROR] DOI not found for dataset 300070: 404
[OK] DOI exists for dataset 100006
...
[ERROR] Some errors were detected. Please check details above.
```

#### Check single doi exists

```
docker-compose run --rm application ./protected/yiic checkdoiexistsindataciteapi --doi=100006
Processing batch of 1 datasets with offset 0
[OK] DOI exists for dataset 100006
[OK] All DOIs are correctly found in the DataCite API.
```

#### Skip first 8 DOIs

```
docker-compose run --rm application ./protected/yiic checkdoiexistsindataciteapi --offset=8
Processing batch of 10 datasets with offset 8
[OK] DOI exists for dataset 100006
[OK] DOI exists for dataset 100020
...
[OK] All DOIs are correctly found in the DataCite API.
```

### Live environment

Same as above for the options, but to run the check command on AWS deployment, from the bastion server:

```
$ docker run --rm -e YII_PATH=/var/www/vendor/yiisoft/yii -v /home/centos:/var/www/protected/runtime registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_app:staging /var/www/protected/yiic checkdoiexistsindataciteapi
```

## Update metadata to the DATACITE API

### Dev environment

#### Batch processing (default 50)

```
$ docker-compose run --rm application ./protected/yiic updatedatasetdatacite
Processing 10 datasets
[OK] Dataset 100035 successfully updated.
[OK] Dataset 100056 successfully updated.
...
Finished processing 10 datasets
```

#### Update datacite metadata in batches of 3

```
$ docker-compose run --rm application ./protected/yiic updatedatasetdatacite --batchSize=3
Processing 3 datasets
[OK] Dataset 100006 successfully updated.
[OK] Dataset 100056 successfully updated.
[OK] Dataset 100020 successfully updated.
...
Finished processing 10 datasets
```

#### Skip first 8 DOIs

```
$ docker-compose run --rm application ./protected/yiic updatedatasetdatacite --offset=8
Processing 2 datasets
[OK] Dataset 100094 successfully updated.
[OK] Dataset 100035 successfully updated.
Finished processing 2 datasets
All datasets processed successfully.
```

#### Update single dataset

```
$ docker-compose run --rm application ./protected/yiic updatedatasetdatacite --doi=100039
Finished processing 0 datasets
All datasets processed successfully.
```

### Live environment

Same as above for the options, but to run the update command on AWS deployment, from the bastion server:

```
docker run --rm -e YII_PATH=/var/www/vendor/yiisoft/yii -v /home/centos:/var/www/protected/runtime registry.gitlab.com/gigascience/forks/rija-gigadb-website/production_app:staging /var/www/protected/yiic updatedatasetdatacite
```

## Optional Parameters:

- Limit (--limit): Defines the batch size (default is 50). If you set a value greater that 450, it will automatically be capped at 450 to comply
with the rate limiter imposed by the Datacite API. 

- Offset (--offset): Specifies the starting point if you want to skip a number of records.
- DOI (--doi): Allows you to update a specific DOI.

## Logging:

you can redirect both standard and error output to a log file using:

```
> my_log.txt 2>&1
```
