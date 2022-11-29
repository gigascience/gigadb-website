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
# List contents of dev directory in gigadb-datasets bucket
$ docker-compose run --rm rclone rclone ls wasabi:gigadb-datasets/dev
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

## Test usage on live server

The CNGB live server has problems with pulling Docker images on the official
registry. To overcome this problem, required images need to be built on your
local machine, saved and then copied to the CNGB backup server:
```
# Ensure you are in cngb-wasabi-migration directory and generate Rclone 
# configuration
$ docker-compose run --rm config

# Build and tag Rclone image
$ docker build -t rclone .
$ docker tag rclone:latest rclone:cngb

# Save images as tar.gz files:
$ docker save alpine:3.16 | gzip > alpine_3_16.tar.gz
$ docker save rija/docker-alpine-shell-tools:1.0.1 | gzip > rija_docker_alpine_shell_tools_1_0_1.tar.gz
$ docker save rclone:cngb | gzip > rclone_cngb.tar.gz
```

You also need to create a tarball of the `cngb-wasabi-migration` directory.

Now transfer all tar.gz files to the CNGB backup server using the Smoc web site. 

When this is done, use the SMOC website to open a shell to the server to load 
the images:
```
# Load images from tar archives
[gigadb@cngb-gigadb-bak ~]$ docker load < rija_docker_alpine_shell_tools_1_0_1.tar.gz
[gigadb@cngb-gigadb-bak ~]$ docker load < alpine_3_16.tar.gz
[gigadb@cngb-gigadb-bak ~]$ docker load < rclone_cngb.tar.gz

# Check images have been loaded
[gigadb@cngb-gigadb-bak ~]$ docker images
```

Also, unzip the `cngb-wasabi-migration` tarball and change directory to it.

To test the loaded Rclone container is working, we use the `rclone_cngb` service
in `docker-compose.yml` which expects a `rclone:cngb` image (which has been 
loaded into the backup server) to be available:
```
# Check version
[gigadb@cngb-gigadb-bak cngb-wasabi-migration]$ docker-compose run --rm rclone_cngb rclone version

# List contents in bucket
[gigadb@cngb-gigadb-bak cngb-wasabi-migration]$ docker-compose run --rm rclone_cngb bash -c 'source /app/proxy_settings.sh; rclone -vv ls wasabi:gigadb-datasets/dev'
```

To use the batch copy script on the CNGB backup server, we need to pass it the 
hostname of the server to the script. The hostname is provided by passing the 
value returned by the `hostname` command which can be called using backticks. By
default, the script will copy/upload the test data that comes with the script:
```
$ docker-compose run --rm -e HOST_HOSTNAME=`hostname` rclone_cngb /app/rclone_copy.sh --starting-doi 100002 --ending-doi 100020
```

If the script determines that it is running on the CNGB backup server then it
will source the required proxy settings and use the appropriate `dev` path for 
the destination to where data set files should be copied to.

Check the latest migration log file in the `logs` directory. The first line
can be interpreted as the script is being run on the CNGB backup server and so
it is sourcing the network proxy settings:
```
2022/11/29 03:18:39 DEBUG  : Sourced proxy settings for CNGB backup server
2022/11/29 03:18:39 DEBUG  : Begin new batch migration to Wasabi
2022/11/29 03:18:39 INFO  : Starting DOI is: 100002
2022/11/29 03:18:39 INFO  : Ending DOI is: 100020
2022/11/29 03:18:39 INFO  : Assessing DOI: 100002
2022/11/29 03:18:39 DEBUG  : Found directory /app/tests/data/cngbdb/giga/gigadb/pub/10.5524/100001_101000/100002
2022/11/29 03:18:39 INFO  : Attempting to copy dataset 100002 to wasabi:gigadb-datasets/dev/pub/10.5524/100001_101000/100002
2022/11/29 03:18:43 INFO  : CR.kegg.gz: Copied (new)
2022/11/29 03:18:43 INFO  : readme.txt: Copied (new)
2022/11/29 03:18:43 INFO  : Successfully copied files to Wasabi for DOI: 100002
2022/11/29 03:18:43 INFO  : Assessing DOI: 100003
```

In addition, you can confirm the test dataset files have been uploaded into the
Wasabi bucket at `gigadb-dataset/dev` using the web console with your Wasabi 
subuser account.

## Production usage on live server

In order for the script to copy `live` GigaDB data, the `--use-live-data` option
should be provided when calling this script as follows:
```
$ docker-compose run --rm -e HOST_HOSTNAME=`hostname` rclone_cngb /app/rclone_copy.sh --starting-doi 100216 --ending-doi 100221 --use-live-data
```

If the `--use-live-data` flag is used and the script determines that it is not 
running on the CNGB server then the script will exit with an error message:
```
[centos@ip-xxx-xx-x-x]$ sudo docker-compose run --rm -e HOST_HOSTNAME=`hostname` rclone /app/rclone_copy.sh --use-live-data --starting-doi 100002 --ending-doi 100020
Cannot copy live data because we are not on backup server - exiting...
```