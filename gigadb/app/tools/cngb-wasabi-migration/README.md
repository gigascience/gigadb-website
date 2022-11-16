# CNGB Wasabi migration tool

This migration tool involves using [rclone](https://rclone.org) to copy dataset 
files from the backup server to a Wasabi bucket.

## Prerequisites

In your GitLab project, two new secret variables need to be created:

| Variable | Options | Environment | Value |
| -------- | ------- | ----------- | ----- |
| WASABI_ACCESS_KEY_ID | Masked | All | Wasabi access key |
| WASABI_SECRET_ACCESS_KEY | Masked | All | Wasabi secret key |

The values of these variables are your Wasabi subuser credentials which you use 
to access Wasabi buckets.

## Configuration

Make a copy of the `env.example` file called `.env`:
```
$ cp env.example .env
```

In the new `.env` file, uncomment and provide a value for the 
`GITLAB_PRIVATE_TOKEN` variable.

Also provide the name of GitLab project fork in the `REPO_NAME` variable.

You can create the configuration file for rclone by executing:
```
$ docker-compose run --rm config
```

Check if the configuration process has worked by looking for the
`config/rclone.conf` file.

## Test Usage

The `docker-compose run` command can be used to create an `rclone` container, 
execute a command in its shell and discard it afterwards. N.B. `-rm` causes 
Docker to automatically remove the container when it exits:
```
$ docker-compose run --rm rclone rclone ls wasabi:
```

The `rclone_copy.sh` script can batch upload a set of datasets as defined by a 
starting DOI and an ending DOI. The script will look for the existence of all 
datasets between these two DOIs. For example, the following command will upload 
two test datasets:
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

If you look at the log file for the above command then you will see an error:
```
2022/11/14 05:06:14 DEBUG  : Begin new batch migration to Wasabi
2022/11/14 05:06:14 INFO  : Starting DOI is: 100002
2022/11/14 05:06:14 INFO  : Ending DOI is: 100304
2022/11/14 05:06:14 ERROR  : Batch size is more that 100 - please reduce size of batch to copy!
```

You might see other error messages if there are other types of problems 
encountered by rclone in the batch copy process. For example:
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

## Running rclone commands in a bash shell

It's also possible to start a Bash session by running the `rclone` container.
You can then execute `rclone` commands in the shell:
```
$ docker-compose run --rm rclone bash
bash-5.1#

# List objects in root source path in Wasabi bucket
bash-5.1# rclone ls wasabi:
    25964 gigadb-datasets/dev/dermatology.data
    39975 gigadb-datasets/dev/pub/10.5524/100001_101000/100002/CR.kegg.gz
```

Datasets that have been uploaded to the `dev` directory in the `gigadb-datasets`
bucket can be quickly deleted using `rclone_reset.sh`:
```
$ docker-compose run --rm rclone /app/rclone_reset.sh
```

## Production usage

To run the batch copy script on CNGB server, we need to pass the hostname of
the server to the script. The hostname is provided by passing the value returned
by the `hostname` command which can be called using backticks:
```
$ docker-compose run --rm -e HOST_HOSTNAME=`hostname` rclone /app/rclone_copy.sh --starting-doi 100002 --ending-doi 100020
```