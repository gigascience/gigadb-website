# CNGB Wasabi migration tool

## Usage

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

## Running rclone commands in a bash shell

It's also possible to start a Bash session by running the `rclone` container.
You can then execute `rclone` commands in the shell:
```
$ docker-compose run --rm rclone bash
bash-5.1#
```

We can then run a command to test rclone connection:
```
# List objects in root source path in Wasabi bucket
bash-5.1# rclone ls wasabi:
    25964 gigadb-datasets/dev/dermatology.data
    39975 gigadb-datasets/dev/pub/10.5524/100001_101000/100002/CR.kegg.gz
```




# Delete directories during dev work
$ docker-compose run --rm rclone /app/rclone_reset.sh

# Running batch copy script on CNGB server
$ docker-compose run --rm -e HOST_HOSTNAME=`cngb-gigadb-bak` rclone /app/rclone_copy.sh --starting-doi 100002 --ending-doi 100304
```