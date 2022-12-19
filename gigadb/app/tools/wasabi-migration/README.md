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

| Variable | Options | Environment | Value               |
| -------- | ------- | ----------- |---------------------|
| GITTER_API_TOKEN | Masked | All | Gitter api token    |
| GITTER_IT_NOTIFICATION_ROOM_ID | Masked | All | Gitter chat room id |

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
# Create a config directory
$ mkdir config
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
two datasets which are fouund in the `tests/data/gigadb/pub/10.5524/100001_1010001`
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
# Stop and remove containers 
% docker stop $(docker ps -aq) && docker rm $(docker ps -aq)
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

## Using migration tool on CNGB Backup Server

### Installation

#### Migration tool source code

On the backup server, we need to use the `Migration` Wasabi sub-user to copy
datasets to the Wasabi bucket. Therefore, the credentials for accessing Wasabi
as the `Migration` sub-user are required. These need to be generated on your
local machine since `docker-compose run --rm config` is not able to access
Gitlab variables from the CNGB backup server.

To generate the `Migration` Wasabi sub-user configuration for rclone, edit
`.env` so that it contains the following details for the `upstream` project:
```
GITLAB_PRIVATE_TOKEN=<The token for upstream user>

REPO_NAME="gigadb-website"
CI_PROJECT_URL="https://gitlab.com/gigascience/upstream/gigadb-website"
GROUP_VARIABLES_URL="https://gitlab.com/api/v4/groups/gigascience/variables?per_page=100"
FORK_VARIABLES_URL="https://gitlab.com/api/v4/groups/3506500/variables"
PROJECT_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2FUpstream%2F$REPO_NAME/variables"
MISC_VARIABLES_URL="https://gitlab.com/api/v4/projects/gigascience%2Fcnhk-infra/variables"
```

Generate the required rclone configuration:
```
# Create a config directory
$ mkdir config
# Create rclone.conf file in config directory
$ docker-compose run --rm config
```

Check that the contents of `rclone.conf` in the `config` directory contains the
credentials for the `Migration` subuser.
```
$ cat config/rclone.conf
```

Create a zipped tarball of the `wasabi-migration` directory:
```
$ cd ..
$ tar -czvf wasabi-migration.tar.gz wasabi-migration
# Move tarball into wasabi-migration directory
$ mv wasabi-migration.tar.gz wasabi-migration
```

#### Images

The CNGB backup server has problems with pulling Docker images from the official
registry. To overcome this problem, these images need to be first built on your
local machine, and then saved and copied to the CNGB backup server:
```
# Ensure you are at the wasabi-migration directory
$ pwd
/path/to/gigadb-website/gigadb/app/tools/wasabi-migration
# Build rclone image
$ docker build -t rclone .
# Tag rclone image
$ docker tag rclone:latest rclone:cngb
# Check there is a new image called rclone with the tag cngb
$ docker images

# Save Docker image as a tar.gz file:
$ docker save rclone:cngb | gzip > rclone_cngb.tar.gz
```

You should now have 2 tar.gz files:
```
$ ls *gz
rclone_cngb.tar.gz                          wasabi-migration.tar.gz
```

These tar.gz files need to uploaded to the CNGB backup server using the SMOC web
site. 

When this is done, use the SMOC website to open an SSH shell to the backup server
to load the image:
```
# Load image from tar archives
[gigadb@cngb-gigadb-bak ~]$ docker load < rclone_cngb.tar.gz

# Check image has been loaded
[gigadb@cngb-gigadb-bak ~]$ docker images
```


Unzip the `wasabi-migration` tarball and change directory to it:
```
[gigadb@cngb-gigadb-bak ~]$ tar -xvf wasabi-migration.tar.gz
[gigadb@cngb-gigadb-bak ~]$ cd wasabi-migration
```

#### Testing migration tool on backup server

To test the loaded rclone container is working, we use the `rclone_cngb` service
in `docker-compose.yml` since this service expects a `rclone:cngb` image (that 
has just been loaded into the backup server) to be available:
```
# Check rclone version used in container
[gigadb@cngb-gigadb-bak cngb-wasabi-migration]$ docker-compose run --rm rclone_cngb rclone version

# List dev directory contents in bucket
[gigadb@cngb-gigadb-bak cngb-wasabi-migration]$ docker-compose run --rm rclone_cngb bash -c 'source /app/proxy_settings.sh; rclone -vv ls wasabi:gigadb-datasets/dev'

# Migration user should also be able to list contents in live directory
[gigadb@cngb-gigadb-bak cngb-wasabi-migration]$ docker-compose run --rm rclone_cngb bash -c 'source /app/proxy_settings.sh; rclone -vv ls wasabi:gigadb-datasets/live'
```

To use the batch copy script on the CNGB backup server, we need to pass it the 
hostname of the server to the script. The hostname is provided by passing the 
value returned by the `hostname` command which can be called using backticks. By
default, the script will copy/upload the test data that comes with the script:
```
[gigadb@cngb-gigadb-bak cngb-wasabi-migration]$ docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi 100002 --ending-doi 100020
```

If the script determines that it is running on the CNGB backup server then it
will source the required proxy settings and copy dataset files to the `staging` 
directory in the Wasabi `gigadb-datasets` bucket.

Check the latest migration log file in the `logs` directory. The first line
can be interpreted as the script confirming that it is being run on the CNGB 
backup server and so it is sourcing the network proxy settings:
```
2022/11/29 03:18:39 DEBUG  : Sourced proxy settings for CNGB backup server
2022/11/29 03:18:39 DEBUG  : Begin new batch migration to Wasabi
2022/11/29 03:18:39 INFO  : Starting DOI is: 100002
2022/11/29 03:18:39 INFO  : Ending DOI is: 100020
2022/11/29 03:18:39 INFO  : Assessing DOI: 100002
2022/11/29 03:18:39 DEBUG  : Found directory /app/tests/data/cngbdb/giga/gigadb/pub/10.5524/100001_101000/100002
2022/11/29 03:18:39 INFO  : Attempting to copy dataset 100002 to wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100002
2022/11/29 03:18:43 INFO  : CR.kegg.gz: Copied (new)
2022/11/29 03:18:43 INFO  : readme.txt: Copied (new)
2022/11/29 03:18:43 INFO  : Successfully copied files to Wasabi for DOI: 100002
2022/11/29 03:18:43 INFO  : Assessing DOI: 100003
```

In addition, you can confirm the test dataset files have been uploaded into the
Wasabi bucket by looking at the contents of `gigadb-dataset/staging` in the web 
console with your Wasabi subuser account.

#### Copying real datasets to Wasabi

In order for the script to copy `live` GigaDB data, the `--use-live-data` option
should be provided when calling the `rclone_copy.sh` script as follows:
```
$ docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi 100216 --ending-doi 100221 --use-live-data
```

This command will copy a set of real GigaDB datasets into the
`gigadb-datasets/live` directory. You can confirm this by browsing the
`gigadb-datasets/live` directory using the Wasabi web console and checking the 
latest log file in the `logs` directory.

If the `--use-live-data` flag is used and the script determines that it is not 
running on the CNGB server then the script will exit with an error message:
```
[centos@ip-xxx-xx-x-x]$ sudo docker-compose run --rm rclone /app/rclone_copy.sh --use-live-data --starting-doi 100002 --ending-doi 100020
Cannot copy live data because we are not on backup server - exiting...
```

#### Batch size for file uploads

The maximum number of datasets that can be uploaded has a default value of 100.
This can be overridden using the `--max-batch-size`. For example, to increase
the batch size to 200:
```
$ docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi 100000 --ending-doi 100300 --max-batch-size 300
```

#### Testing the notification feature if error occurs during the backup process
```
# Spin up the log monitoring service 
% docker-compose up -d swatchdog_cngb
# Check the swatchdog state
% docker-compose ps 
[gigadb@cngb-gigadb-bak wasabi-migration]$ docker-compose ps
             Name                            Command               State   Ports
--------------------------------------------------------------------------------
wasabi-                           swatchdog -c /app/config/s ...   Up           
migration_swatchdog_cngb_1      
# To generate log file containing ERROR
[gigadb@cngb-gigadb-bak wasabi-migration]$ docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi 100002 --ending-doi 100320 
# Check the log file can be found in the logs/ dir
# Stop and remove rclone container 
[gigadb@cngb-gigadb-bak wasabi-migration]$ docker stop $(docker ps -aq) && docker rm $(docker ps -aq)
```
