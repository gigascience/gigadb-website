# CNGB Wasabi migration tool

This migration tool uses [rclone](https://rclone.org) to batch copy dataset 
files from the backup server to a Wasabi bucket.

If the backup process has been stopped/exited unexpectedly, the error message will be sent
to a gitter chatroom. This notification feature is employed by using [swatchdog](https://github.com/ToddAtkins/swatchdog).

## Prerequisites

In your GitLab project, two new secret variables need to be created:

| Variable | Options | Environment | Value |
| -------- | ------- | ----------- | ----- |
| WASABI_ACCESS_KEY_ID | Masked | All | Wasabi access key |
| WASABI_SECRET_ACCESS_KEY | Masked | All | Wasabi secret key |

The values of these variables are your Wasabi subuser credentials which you use 
to access Wasabi buckets.

To enable the notification feature, the following credentials are also required:

| Variable                       | Options | Environment | Value                  |
|--------------------------------|--------|-------------|------------------------|
| MATRIX_HOMESERVER              | ------- | All         | Matrix home server     |
| MATRIX_TOKEN                   | Masked | All         | Matrix api token       |
| MATRIX_IT_NOTIFICATION_ROOM_ID | Masked | All         | Matrix IT chat room id |

Both `MATRIX_HOMESERVER  `, `MATRIX_TOKEN` and `MATRIX_IT_NOTIFICATION_ROOM_ID` have been 
defined in the `cngb-infra` sub-group so, as a developer, there is no need to create
these three variables. Additional information, the `MATRIX_TOKEN` is for the user `gigatech23`,
a specific user account for the tech team testing.

## Using migration tool on dev environment

#### Configuration

Change directory to the `gigadb-website/gigadb/app/tools/wasabi-migration`
directory and make a copy of the `env.example` file called `.env`:
```
$ cd gigadb/app/tools/wasabi-migration
$ cp env.example .env
```

In the new `.env` file, uncomment and provide a value for the 
`GITLAB_PRIVATE_TOKEN` variable.

Also provide the name of GitLab project fork in the `REPO_NAME` variable.

You should then be able to create the configuration file for rclone by 
executing:
```
$ docker-compose run --rm config
```

Check if the configuration process has worked by looking for the
`config/rclone.conf` file.

#### Test Usage

The `docker-compose run` command can be used to create an `rclone` container, 
execute a command in its shell. It can then be discarded afterwards using `-rm` 
which causes Docker to automatically remove the container when it exits:
```
# List contents of dev directory in gigadb-datasets bucket
$ docker-compose run --rm rclone rclone ls wasabi:gigadb-datasets/dev
Creating wasabi-migration_rclone_run ... done
    25964 dermatology.data
    39975 pub/10.5524/100001_101000/100002/CR.kegg.gz
      649 pub/10.5524/100001_101000/100002/readme.txt
```

The `rclone_copy.sh` script can batch upload a set of datasets as defined by a 
starting DOI and an ending DOI. The script will look for the existence of all 
datasets between these two DOIs. For example, the following command will upload 
two datasets which are found in the `tests/data/gigadb/pub/10.5524/100001_1010001`
directory:
```
$ docker-compose run --rm rclone /app/rclone_copy.sh --starting-doi 100001 --ending-doi 100020
```

Status messages are created during the batch copy process. These messages are
saved into log files located in the `/app/logs` directory in the container. The
contents of the log files will look like this:
```
2022/11/10 09:37:17 DEBUG  : Starting new batch copy process to Wasabi
2022/11/10 09:37:17 INFO  : Starting DOI is: 100001
2022/11/10 09:37:17 INFO  : Ending DOI is: 100020
2022/11/10 09:37:17 INFO  : Assessing DOI: 100001
2022/11/10 09:37:17 INFO  : Assessing DOI: 100002
2022/11/10 09:37:17 DEBUG  : Directory: /cngbdb/giga/gigadb/pub/10.5524/100001_101000/100002 exists
2022/11/10 09:37:17 DEBUG  : Attempting to copy dataset 100002 to Wasabi...
2022/11/10 09:37:17 INFO  : readme.txt: Copied (new)
2022/11/10 09:37:17 INFO  : CR.kegg.gz: Copied (new)
```

The script will restrict the size of the batch of datasets that can be uploaded
to 100 datasets:
```
$ docker-compose run --rm rclone /app/rclone_copy.sh --starting-doi 100002 --ending-doi 100304
```

If you look at the latest file in `logs` directory for the above command then 
you will see an error:
```
2022/11/14 05:06:14 DEBUG  : Begin new batch migration to Wasabi
2022/11/14 05:06:14 INFO  : Starting DOI is: 100002
2022/11/14 05:06:14 INFO  : Ending DOI is: 100304
2022/11/14 05:06:14 ERROR  : Batch size is more that 100 - please reduce size of batch to copy!
```

You might see other error messages if there are other types of problems 
encountered by rclone in the batch copy process. For example, the messages below
come from the rclone tool itself:
```
2022/11/08 03:51:28 ERROR : CR.kegg.gz: Failed to copy: Forbidden: Forbidden
	status code: 403, request id: 995175177AB4CB74, host id: 9fAA3voAuShWb+iFGMlGuH1xfLTF6gVyOz+/ru8VKkf/JZFvOFl+pBmRZHtMiaFppWlHFHFKA3Au
2022/11/08 03:51:28 ERROR : Attempt 1/3 failed with 2 errors and: Forbidden: Forbidden
	status code: 403, request id: 995175177AB4CB74, host id: 9fAA3voAuShWb+iFGMlGuH1xfLTF6gVyOz+/ru8VKkf/JZFvOFl+pBmRZHtMiaFppWlHFHFKA3Au
2022/11/08 03:51:28 NOTICE: CR.kegg.gz: Failed to read metadata: Forbidden: Forbidden
	status code: 403, request id: BD49F9DE452837D3, host id: wKdDU2SUMOCqbQ7NtZJpkN+WfCKIAO7AsGBVYc6SbeaauUfDa0KaZ9KZ0i7vZUZs4DRu9ruScskQ
2022/11/08 03:51:28 ERROR : CR.kegg.gz: Failed to set modification time: Forbidden: Forbidden
	status code: 403, request id: 739C8682DB01F4BF, host id: hLRhJE+0x1+coHm/NPs6gYtilvnIQHG+GnmcAeKMuiiKwPhLe/J3TcHHaUT86U7eocqtQKU+Zr67
2022/11/08 03:51:28 INFO  : There was nothing to transfer
2022/11/08 03:51:28 ERROR : Attempt 2/3 failed with 2 errors and: Forbidden: Forbidden
	status code: 403, request id: 33B69D72246B9D3C, host id: F177yh/sVvSJDNo+gDFDCe8w+bgsYqUc9bGF4GtmBE3mp0yHk+mJJ8aul245snEbfypetO5yPHIW
2022/11/08 03:51:29 ERROR : Attempt 3/3 failed with 2 errors and: Forbidden: Forbidden
	status code: 403, request id: 4FD236673B39B110, host id: iAbakIt14agdiNaRUxKsezfAO8b2Eh6ESeXLqdqZaWsXfNV8iUlTPXnAuGbBih3Fe71/HA3tgnyU
2022/11/08 03:51:29 Failed to copy with 2 errors: last error was: Forbidden: Forbidden
	status code: 403, request id: 4FD236673B39B110, host id: iAbakIt14agdiNaRUxKsezfAO8b2Eh6ESeXLqdqZaWsXfNV8iUlTPXnAuGbBih3Fe71/HA3tgnyU
```

#### Using `migrate.sh` to start swatchdog and data migration

The `migrate.sh` is a bash script which is able to spin up the swatchdog 
monitoring service, start the backup process and stop the container as the 
house-keeping step. To test `migrate.sh` in a `dev` environment, it requires 
three  arguments: starting DOI, ending DOI, and maximum batch size:
```
# Test migrate 2 datasets
$ ./migrate.sh 100001 100020 100
```

If you now go to the Wasabi web console and look in 
`Buckets/gigadb-datasets/dev/pub/10.5524/100001_101000` then you will see two
datasets that have DOIs: 100002 and 100012 uploaded to the ***dev***
directory of the `gigadb-datasets` bucket. The latest `logs/log` file should 
also report the transfer of the two datasets.

The Swatchdog notification service can be tested if you try to migrate a batch 
size of datasets that it over the maximum allowed. The command below will try to
upload over 300 datasets but the maximum batch size has been configured to be
100:
```
# Test batch size too big
$ ./migrate.sh 100002 100304 100
```

A message in the Gitter room `GigaScience-IT-Notification` should appear:
```
gigatech23 @gigatech23 09:52
(Drill) rclone.docker : 2023/01/21 01:52:07 ERROR : Batch size is more than 100 - please reduce size of batch to copy!
```

***

#### Testing the notification feature if error occurs during the backup process
```
# Spin up the log monitoring service 
% docker-compose up -d swatchdog
# Check the swatchdog state
% docker-compose ps 
            Name                          Command               State   Ports
-----------------------------------------------------------------------------
wasabi-migration_swatchdog_1   swatchdog -c /app/config/s ...   Up           
# To generate log file containing ERROR
% docker-compose run --rm rclone /app/rclone_copy.sh --starting-doi 100001 --ending-doi 100320
# Check the log file can be found in the logs/ dir
# Check the ERROR message in the gitter room
# Stop the container
% docker-compose stop swatchdog
# Or execute the wrapper script
% ./migrate.sh 100001 100320 100
```

#### Running rclone commands in a bash shell

It's possible to start a bash session by running the `rclone` container which
can then be used to execute `rclone` commands in the shell:
```
$ docker-compose run --rm rclone bash
bash-5.1#

# List objects in root source path in Wasabi bucket
bash-5.1# rclone ls wasabi:
    25964 gigadb-datasets/dev/dermatology.data
    39975 gigadb-datasets/dev/pub/10.5524/100001_101000/100002/CR.kegg.gz
```

