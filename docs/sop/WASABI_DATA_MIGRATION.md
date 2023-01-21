# SOP: Using Wasabi migration tool on CNGB Backup Server

## Installation

### Migration tool source code

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

### Images

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
# Build swatchdog image
$ docker build -f Swatchdog-Dockerfile -t swatchdog .
# Tag swatchdog image
$ docker tag swatchdog:latest swatchdog:cngb
# Check there are new images called rclone and swatchdog with the tag cngb
$ docker images
REPOSITORY                       TAG                  IMAGE ID       CREATED         SIZE
rclone                           cngb                 ccd22e890601   7 minutes ago   61.3MB
rclone                           latest               ccd22e890601   7 minutes ago   61.3MB
swatchdog                        cngb                 44d987ded07f   12 days ago         60.3MB
swatchdog                        latest               44d987ded07f   12 days ago         60.3MB

# Save Docker image as a tar.gz file:
$ docker save rclone:cngb | gzip > rclone_cngb.tar.gz
$ docker save swatchdog:cngb | gzip > swatchdog_cngb.tar.gz
```

You should now have 3 tar.gz files:
```
$ ls -lh *.gz
-rw-r--r--  1 peterli  staff    22M Jan 18 16:50 rclone_cngb.tar.gz
-rw-r--r--  1 peterli  staff    17M Jan 18 16:50 swatchdog_cngb.tar.gz
-rw-r--r--  1 peterli  staff   195K Jan 18 16:09 wasabi-migration.tar.gz
```

Upload these tar.gz files to the CNGB backup server using the SMOC web site to 
`/home/gigadb/migration`.

Prior to using these images to spin up containers, pre-existing images and 
containers should be stopped and removed. Use the SMOC website to open an 
SSH shell to execute the following commands:
```
# List running containers
[gigadb@cngb-gigadb-bak ~]$ docker ps
CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS               NAMES
03a28b390831        swatchdog:cngb      "swatchdog -c /app..."   13 days ago         Up 13 days                              wasabi-migration_swatchdog_cngb_1
# Stop and remove container
[gigadb@cngb-gigadb-bak ~]$ docker stop wasabi-migration_swatchdog_cngb_1
[gigadb@cngb-gigadb-bak migration]$ docker rm wasabi-migration_swatchdog_cngb_1

# List images
$ docker images                   
REPOSITORY                       TAG                 IMAGE ID            CREATED             SIZE
rclone                           cngb                d90f6007c9b0        13 days ago         61.3 MB
swatchdog                        cngb                83179cd2a45a        13 days ago         60.3 MB
alpine                           3.16                bfe296a52501        2 months ago        5.54 MB
rija/docker-alpine-shell-tools   1.0.1               3bdd20ea7a29        4 years ago         15.3 MB
# Delete images
[gigadb@cngb-gigadb-bak ~]$ docker image rm rclone:cngb
Untagged: rclone:cngb
Deleted: sha256:d90f6007c9b08c4a84fa6cd97f50854bf4ed2005eecfec310d4ba025220a197d
Deleted: sha256:25ca486f7a581b516e2630ce8a1d1947ddf287772915b3fc8d333b18ac4c7f7e
[gigadb@cngb-gigadb-bak ~]$ docker image rm swatchdog:cngb
```

Now, load the new images into the Docker server:
```
# Load images from tar archives
[gigadb@cngb-gigadb-bak ~]$ docker load < rclone_cngb.tar.gz
[gigadb@cngb-gigadb-bak ~]$ docker load < swatchdog_cngb.tar.gz 

# Check image has been loaded
[gigadb@cngb-gigadb-bak ~]$ docker images
REPOSITORY                       TAG                 IMAGE ID            CREATED             SIZE
rclone                           cngb                ccd22e890601        20 minutes ago      61.3 MB
swatchdog                        cngb                44d987ded07f        12 days ago         60.3 MB
```

Unzip the `wasabi-migration` tarball and change directory to it:
```
[gigadb@cngb-gigadb-bak ~]$ tar -xvf wasabi-migration.tar.gz
[gigadb@cngb-gigadb-bak ~]$ cd wasabi-migration
```

### Checking migration tool works on backup server

To test the loaded rclone container is working, we use the `rclone_cngb` service
in `docker-compose.yml` since this service expects a `rclone:cngb` image (that
has just been loaded into the backup server) to be available:
```
# Check rclone version used in container
[gigadb@cngb-gigadb-bak wasabi-migration]$ docker-compose run --rm rclone_cngb rclone version
rclone v1.60.0
- os/version: alpine 3.16.3 (64 bit)
- os/kernel: 3.10.0-862.14.4.el7.x86_64 (x86_64)
- os/type: linux
- os/arch: amd64
- go/version: go1.19.2
- go/linking: static
- go/tags: none
# Check swatchdog version used in container
[gigadb@cngb-gigadb-bak wasabi-migration]$ docker-compose run --rm swatchdog_cngb swatchdog --version
This is swatchdog version 3.2.4
Built on Aug 25, 2008
Built by E. Todd Atkins <Todd.Atkins@StanfordAlumni.ORG>
# List dev directory contents in bucket
[gigadb@cngb-gigadb-bak wasabi-migration]$ docker-compose run --rm rclone_cngb bash -c 'source /app/proxy_settings.sh; rclone -vv ls wasabi:gigadb-datasets/dev'
# Migration user should also be able to list contents in live directory
[gigadb@cngb-gigadb-bak wasabi-migration]$ docker-compose run --rm rclone_cngb bash -c 'source /app/proxy_settings.sh; rclone -vv ls wasabi:gigadb-datasets/live'
```

To use the batch copy script on the CNGB backup server, we need to pass it the
hostname of the server to the script. The hostname value is embedded in the 
`docker-compose.yml` file, as an environment variable. This helps to shorten the 
command, and make the container services more specific to the CNGB backup 
server. By default, the script will copy/upload the test data that comes with
the script:
```
[gigadb@cngb-gigadb-bak]$ docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi 100002 --ending-doi 100020
```

If the script determines that it is running on the CNGB backup server then it
will source the required proxy settings and copy dataset files to the `staging`
directory in the Wasabi `gigadb-datasets` bucket.

Check the latest migration log file in the `logs` directory. The first line
can be interpreted as the script confirming that it is being run on the CNGB
backup server and so it is sourcing the network proxy settings:
```
[gigadb@cngb-gigadb-bak]$ head logs/migration_20230118_111722.log 
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

We can also list the files in the `staging` directory to get confirmation:
```
[gigadb@cngb-gigadb-bak]$ docker-compose run --rm rclone_cngb bash -c 'source /app/proxy_settings.sh; rclone -vv ls wasabi:gigadb-datasets/staging'
2023/01/18 11:37:10 DEBUG : rclone: Version "v1.60.0" starting with parameters ["rclone" "-vv" "ls" "wasabi:gigadb-datasets/staging"]
2023/01/18 11:37:10 DEBUG : Creating backend with remote "wasabi:gigadb-datasets/staging"
2023/01/18 11:37:10 DEBUG : Using config file from "/root/.config/rclone/rclone.conf"
    79710 Aldabra_Giant_Tortoise.png
    39975 pub/10.5524/100001_101000/100002/CR.kegg.gz
      649 pub/10.5524/100001_101000/100002/readme.txt
     2696 pub/10.5524/100001_101000/100012/Keller.gff
     3164 pub/10.5524/100001_101000/100012/readme.txt
  5229469 pub/10.5524/100001_101000/100216/Badhwar_HBM_Brainspell-master.zip
   618633 pub/10.5524/100001_101000/100217/Cipollini_HBM_NiData-master.zip
   847985 pub/10.5524/100001_101000/100218/clark_mx15_bids-master.zip
  2366391 pub/10.5524/100001_101000/100219/Craddock-AMX-Centrality-master.zip
  1306938 pub/10.5524/100001_101000/100220/Das_HBM_LORIS-master.zip
  1049584 pub/10.5524/100001_101000/100221/Dery_HBM_ClubsOfScience-master.zip
```

In addition, you can confirm the test dataset files have been uploaded into the
Wasabi bucket by looking at the contents of `gigadb-datasets/staging` in the web
console with your Wasabi subuser account.

### Dataset file deletion

To remove a dataset file from the `staging` directory:
```
# Do dry run first
[gigadb@cngb-gigadb-bak]$ docker-compose run --rm rclone_cngb bash -c 'source /app/proxy_settings.sh; rclone --dry-run delete wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100002/CR.kegg.gz'
2023/01/18 12:45:38 NOTICE: CR.kegg.gz: Skipped delete as --dry-run is set (size 39.038Ki)

# Delete file - looks like migration user cannot delete files from staging directory
[gigadb@cngb-gigadb-bak]$ docker-compose run --rm rclone_cngb bash -c 'source /app/proxy_settings.sh; rclone delete wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100002/CR.kegg.gz'
```

### Using migration tool to copy live data to Wasabi

The `rclone_copy.sh` script can be used to copy `live` GigaDB data by adding the
`--use-live-data` option as follows:
```
[gigadb@cngb-gigadb-bak]$ docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi 100216 --ending-doi 100221 --use-live-data
```

This command will copy a set of real GigaDB datasets into the
`gigadb-datasets/live` directory. You can confirm this by browsing to this 
directory using the Wasabi web console as well as checking the latest log file 
in the `logs` directory.

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
[gigadb@cngb-gigadb-bak]$ docker-compose run --rm rclone_cngb /app/rclone_copy.sh --starting-doi 100000 --ending-doi 100300 --max-batch-size 300
```

### Using `migrate.sh` to start swatchdog and data migration process

#### Test usage

`migrate.sh` is a bash script to spin up swatchdog, start the migration process
and remove the containers as the house-keeping step. Data migration on the 
CNGB backup server with `migrate.sh` can be tested by uploading test data to 
`wasabi:gigadb-datasets/staging` using three command-line arguments: a starting 
DOI, ending DOI and maximum allowed batch size:
```
[gigadb@cngb-gigadb-bak]$ ./migrate.sh 100002 100012 100
```

A log of the migration process can be viewed by looking at the latest log file
in the `logs` directory:
```
[gigadb@cngb-gigadb-bak]$ more logs/migration_20230120_001031.log 
2023/01/20 00:10:31 DEBUG  : Sourced proxy settings for CNGB backup server
2023/01/20 00:10:31 DEBUG  : Begin new batch migration to Wasabi
2023/01/20 00:10:31 INFO  : Starting DOI is: 100002
2023/01/20 00:10:31 INFO  : Ending DOI is: 100012
2023/01/20 00:10:31 INFO  : Assessing DOI: 100002
2023/01/20 00:10:31 DEBUG  : Found directory /app/tests/data/gigadb/pub/10.5524/100001_101000/100002
2023/01/20 00:10:31 INFO  : Attempting to copy dataset 100002 to wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100002
2023/01/20 00:10:33 INFO  : readme.txt: Copied (new)
2023/01/20 00:10:33 INFO  : CR.kegg.gz: Copied (new)
2023/01/20 00:10:33 INFO  : Successfully copied files to Wasabi for DOI: 100002
```

If you now go to the Wasabi web console and look in
`Buckets/gigadb-datasets/staging/pub/10.5524/100001_101000` then you will see 
two datasets that have DOIs: 100002 and 100012 uploaded to the ***staging***
directory of the `gigadb-datasets` bucket. The latest `logs/log` file should
also report the transfer of the two datasets.

#### Live usage to migrate production data

To migrate real GigaDB datasets on the CNGB backup server to the 
`wasabi:gigadb-datasets/live` directory, an additional fourth command-line 
`true` argument is required to inform the migration process should transfer live 
data:
```
[gigadb@cngb-gigadb-bak]$ ./migrate.sh 100216 100218 100 true
Starting wasabi-migration_swatchdog_cngb_1 ... done
Stopping wasabi-migration_swatchdog_cngb_1 ... done
```
> Before running the above command, you will need to delete three datasets to
> from the `live` directory to see them uploaded. If you are logged into the 
> Wasabi web console, use the Switch Role functionality to gain Admin access to 
> delete the 100216, 100217 and 100218 datasets.

Check latest log file. You should see reporting of the datasets uploaded into 
`wasabi:gigadb-datasets/live`:
```
$ more logs/migration_20230120_013724.log 
2023/01/20 01:37:24 DEBUG  : Sourced proxy settings for CNGB backup server
2023/01/20 01:37:24 INFO  : Updated paths to data for CNGB backup server
2023/01/20 01:37:24 DEBUG  : Begin new batch migration to Wasabi
2023/01/20 01:37:24 INFO  : Starting DOI is: 100216
2023/01/20 01:37:24 INFO  : Ending DOI is: 100218
2023/01/20 01:37:24 INFO  : Assessing DOI: 100216
2023/01/20 01:37:24 DEBUG  : Found directory /live-data/gigadb/pub/10.5524/100001_101000/100216
2023/01/20 01:37:24 INFO  : Attempting to copy dataset 100216 to wasabi:gigadb-datasets/live/pub/10.5524/100001_101000/100216
2023/01/20 01:37:36 INFO  : Badhwar_HBM_Brainspell-master.zip: Copied (new)
2023/01/20 01:37:36 INFO  : Successfully copied files to Wasabi for DOI: 100216
2023/01/20 01:37:36 INFO  : Assessing DOI: 100217
2023/01/20 01:37:36 DEBUG  : Found directory /live-data/gigadb/pub/10.5524/100001_101000/100217
2023/01/20 01:37:36 INFO  : Attempting to copy dataset 100217 to wasabi:gigadb-datasets/live/pub/10.5524/100001_101000/100217
2023/01/20 01:37:37 INFO  : There was nothing to transfer
2023/01/20 01:37:37 INFO  : Successfully copied files to Wasabi for DOI: 100217
2023/01/20 01:37:37 INFO  : Assessing DOI: 100218
2023/01/20 01:37:37 DEBUG  : Found directory /live-data/gigadb/pub/10.5524/100001_101000/100218
2023/01/20 01:37:37 INFO  : Attempting to copy dataset 100218 to wasabi:gigadb-datasets/live/pub/10.5524/100001_101000/100218
2023/01/20 01:37:39 INFO  : There was nothing to transfer
2023/01/20 01:37:39 INFO  : Successfully copied files to Wasabi for DOI: 100218
2023/01/20 01:37:39 INFO  : Finished batch copy process to Wasabi
```

Also, go to the Wasabi web console and look in
`Buckets/gigadb-datasets/live/pub/10.5524/100001_101000`. You should see three
datasets that have DOIs: 100002 and 100012 uploaded to the ***live***
directory of the `gigadb-datasets` bucket.

#### Error notification testing with migrate.sh script

We can test the Swatchdog notification service is working by trying to migrate a
number of datasets that is greater than the maximum allowed batch size:
```
# Max batch size is less than number of datasets to be migrated
[gigadb@cngb-gigadb-bak]$ ./migrate.sh 100001 100320 100
```

If you look at the `GigaScience-IT-Notification` room, you should see the 
following message:
```
gigatech23 @gigatech23 10:35
CNGB BACKUP SERVER : 2023/01/21 02:36:19 ERROR : Batch size is more than 100 - please reduce size of batch to copy!
```
