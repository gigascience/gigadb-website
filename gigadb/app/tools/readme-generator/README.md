# README GENERATOR TOOL

## Preparation

In the `gigadb-website` repo root directory, spin up the GigaDB application
since we need access to the `database` container for the `gigadb` and 
`gigadb_testdata` databases.
```
$ pwd
/path/to/gigadb-website
$ ./up.sh
```

Now change directory to the `readme-generator` folder
```
$ cd gigadb/app/tools/readme-generator
```

Create a `.env` file:
```
$ cp config-sources/env.example .env
```
> Ensure you have provided values for `GITLAB_PRIVATE_TOKEN` and `REPO_NAME`
> variables.

Generate config files:
```
# Generate configuration using variables in .env, GitLab, then exit
$ docker-compose run --rm configure
```
> db.php and test_db.php should be present in the `config` directory. There 
> should be a runtime/curators directory too.

Install Composer dependencies:
```
$  docker-compose run --rm tool composer install 
```

The copying of readme files created by this tool into Wasabi requires Rclone
to be installed on your `dev` machine. This can be done using as follows:
```
# Using Homebrew
$ brew install rclone
# Or using Macports
$ sudo port install rclone
```

> There is an analogous step in the Ansible playbook for the bastion server 
> for installing Rclone on staging and live environments.
> ansible-galaxy install -r ../../../infrastructure/requirements.yml

The create readme tool uses the rclone configuration file from the wasabi
migration tool. Change directory to the `gigadb-website/gigadb/app/tools/wasabi-migration`
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

## How to test

Ensure you have `bats` installed (e.g: on macOS, you could do `brew install bats-core`
or `port install bats-core`). Then run:
```
# Ensure you are in gigadb/app/tools/readme-generator directory
$ bats tests
 ✓ create readme file
 ✓ check does not create readme with invalid doi and exits
 ✓ create one readme file using batch mode
 ✓ create two readme files using batch mode

4 tests, 0 failures
```


## Using readme generator tool in dev environment

The readme information for a dataset can be viewed on standard output using its
DOI:
```
$ docker-compose run --rm tool /app/yii readme/create --doi 100142 --outdir /home/curators  --bucketPath wasabi:gigadb-datasets/dev/pub/10.5524
```

The `--bucketPath` variable is essential for executing the readme tool as a command line tool,
it is needed for constructing the location path in the `File` table as below:

| location                                                                                                       |
|----------------------------------------------------------------------------------------------------------------|
| https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100142/readme_100142.txt |

Once the tool has been executed successfully, an entry in the `file` table will be updated/created with the updated name, location and file size,
and an entry in the `file_attributes` will be created with attribute_id `605` and the md5 value.

Information for the readme is retrieved from the `database` container that was
spun up using the `up.sh` command above. The tool is able to connect to this
container by connecting to the Docker `db-tier` network.

Saving the readme information into a file requires a file path, for example:
```
$ docker-compose run --rm tool /app/yii readme/create --doi 100142 --outdir /home/curators --bucketPath wasabi:gigadb-datasets/dev/pub/10.5524
```
Since `/home/curators` has been mounted to `runtime/curators` directory in
`docker-compose.yml`, you should find a `readme_100142.txt` created there after
running the above command.


## Using readme generator tool via shell wrapper script in dev environment

There is a shell script which can be used to call the readme tool:
```
$ ./createReadme.sh --doi 100142 --outdir /home/curators
```

The `--bucketPath` variable here is not necessary, as it will be supplied to the
tool inside the script.

You should see a `readme_100142.txt` file created in runtime/curators directory.
There will also be a new log file created in uploadDir/ directory which is named:
`readme_100142_yyyymmdd_hhmmss.log`.

In the absence of an output directory `outdir` parameter value or if the
directory cannot be created then an error message will be displayed:
```
$ ./createReadme.sh --doi 100142 --outdir /home/foo
Cannot save readme file - Output directory does not exist or is not a directory
```

The corresponding log file will confirm this:
```
2023/09/01 10:51:43 ERROR  : Could not save readme file for DOI 100142 at /home/foo
```

An error message is also displayed if a DOI is provided for a dataset that does 
not exist:
```
$ ./createReadme.sh --doi 1
Dataset 1 not found
```

### Wasabi upload of readme files

The readme tool is also able to copy readme files it creates into Wasabi:
```
# Execute wasabi upload in dry run mode
$ ./createReadme.sh --doi 100142 --outdir /home/curators --wasabi
```

The latest log file will confirm dry run mode Wasabi upload:
```
2023/09/01 11:13:58 INFO  : Created readme file for DOI 100142 in /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100142.txt
2023/09/01 11:14:03 NOTICE: readme_100142.txt: Skipped update modification time as --dry-run is set (size 1.603Ki)
2023/09/01 11:14:03 NOTICE: 
Transferred:   	          0 B / 0 B, -, 0 B/s, ETA -
Elapsed time:         5.1s

2023/09/01 11:14:03 INFO  : Executed: rclone copy --s3-no-check-bucket /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100142.txt wasabi:gigadb-datasets/dev/pub/10.5524/100001_101000/100142/ --config ../wasabi-migration/config/rclone.conf --dry-run --log-file /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/logs/readme_100142_20230901_111357.log --log-level INFO --stats-log-level DEBUG >> /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/logs/readme_100142_20230901_111357.log
2023/09/01 11:14:03 INFO  : Successfully copied file to Wasabi for DOI: 100142
```

Using the `--apply` flag will switch off dry run mode and copy the readme file
into the gigadb-datasets/dev bucket:
```
# Confirm actual wasabi upload of files using apply flag
$ ./createReadme.sh --doi 100142 --outdir /home/curators --wasabi --apply
```

The latest log file should confirm Wasabi upload:
```
2023/09/01 11:18:20 INFO  : Created readme file for DOI 100142 in /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100142.txt
2023/09/01 11:18:21 INFO  : readme_100142.txt: Updated modification time in destination
2023/09/01 11:18:21 INFO  : Executed: rclone copy --s3-no-check-bucket /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100142.txt wasabi:gigadb-datasets/dev/pub/10.5524/100001_101000/100142/ --config ../wasabi-migration/config/rclone.conf --log-file /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/logs/readme_100142_20230901_111819.log --log-level INFO --stats-log-level DEBUG >> /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/logs/readme_100142_20230901_111819.log
2023/09/01 11:18:21 INFO  : Successfully copied file to Wasabi for DOI: 100142
```

### Batch processing of readme files

The createReadme.sh script has a batch processing mode which can be accessed
using the `--batch` flag:
```
# Create 2 readme files
$ ./createReadme.sh --doi 100005 --outdir /home/curators --batch 2
```

The `--batch` flag takes a value equal to the number of readme files you would
like to successfully create. The above command will create a total of 2 readme
files which its log file will confirm:

```
2023/09/01 11:31:55 WARN  : No dataset for DOI 100005
2023/09/01 11:31:57 INFO  : Created readme file for DOI 100006 in /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100006.txt
2023/09/01 11:31:58 WARN  : No dataset for DOI 100007
2023/09/01 11:31:59 WARN  : No dataset for DOI 100008
2023/09/01 11:32:00 WARN  : No dataset for DOI 100009
2023/09/01 11:32:01 WARN  : No dataset for DOI 100010
2023/09/01 11:32:02 WARN  : No dataset for DOI 100011
2023/09/01 11:32:03 WARN  : No dataset for DOI 100012
2023/09/01 11:32:03 WARN  : No dataset for DOI 100013
2023/09/01 11:32:04 WARN  : No dataset for DOI 100014
2023/09/01 11:32:05 WARN  : No dataset for DOI 100015
2023/09/01 11:32:06 WARN  : No dataset for DOI 100016
2023/09/01 11:32:06 WARN  : No dataset for DOI 100017
2023/09/01 11:32:07 WARN  : No dataset for DOI 100018
2023/09/01 11:32:08 WARN  : No dataset for DOI 100019
2023/09/01 11:32:10 INFO  : Created readme file for DOI 100020 in /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100020.txt
```

Batch processing will work with Wasabi upload of readme files too. For example:
```
# Execute wasabi upload in dry run mode
$ ./createReadme.sh --doi 100005 --outdir /home/curators --wasabi --batch 2
```

The corresponding log file will confirm the dry run upload of 2 readme files:
```
2023/09/01 11:39:25 WARN  : No dataset for DOI 100005
2023/09/01 11:39:25 INFO  : Created readme file for DOI 100006 in /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100006.txt
2023/09/01 11:39:30 NOTICE: readme_100006.txt: Skipped copy as --dry-run is set (size 2.461Ki)
2023/09/01 11:39:30 NOTICE: 
Transferred:   	    2.461 KiB / 2.461 KiB, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         4.7s

2023/09/01 11:39:30 INFO  : Executed: rclone copy --s3-no-check-bucket /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100006.txt wasabi:gigadb-datasets/dev/pub/10.5524/100001_101000/100006/ --config ../wasabi-migration/config/rclone.conf --dry-run --log-file /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/logs/readme_100005_20230901_113924.log --log-level INFO --stats-log-level DEBUG >> /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/logs/readme_100005_20230901_113924.log
2023/09/01 11:39:30 INFO  : Successfully copied file to Wasabi for DOI: 100006
2023/09/01 11:39:31 WARN  : No dataset for DOI 100007
2023/09/01 11:39:32 WARN  : No dataset for DOI 100008
2023/09/01 11:39:32 WARN  : No dataset for DOI 100009
2023/09/01 11:39:33 WARN  : No dataset for DOI 100010
2023/09/01 11:39:34 WARN  : No dataset for DOI 100011
2023/09/01 11:39:35 WARN  : No dataset for DOI 100012
2023/09/01 11:39:36 WARN  : No dataset for DOI 100013
2023/09/01 11:39:36 WARN  : No dataset for DOI 100014
2023/09/01 11:39:37 WARN  : No dataset for DOI 100015
2023/09/01 11:39:38 WARN  : No dataset for DOI 100016
2023/09/01 11:39:39 WARN  : No dataset for DOI 100017
2023/09/01 11:39:39 WARN  : No dataset for DOI 100018
2023/09/01 11:39:40 WARN  : No dataset for DOI 100019
2023/09/01 11:39:41 INFO  : Created readme file for DOI 100020 in /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100020.txt
2023/09/01 11:39:41 NOTICE: readme_100020.txt: Skipped copy as --dry-run is set (size 2.002Ki)
2023/09/01 11:39:41 NOTICE: 
Transferred:   	    2.002 KiB / 2.002 KiB, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.3s

2023/09/01 11:39:41 INFO  : Executed: rclone copy --s3-no-check-bucket /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/runtime/curators/readme_100020.txt wasabi:gigadb-datasets/dev/pub/10.5524/100001_101000/100020/ --config ../wasabi-migration/config/rclone.conf --dry-run --log-file /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/logs/readme_100005_20230901_113924.log --log-level INFO --stats-log-level DEBUG >> /Volumes/PLEXTOR/PhpstormProjects/pli888/gigadb-website/gigadb/app/tools/readme-generator/logs/readme_100005_20230901_113924.log
2023/09/01 11:39:41 INFO  : Successfully copied file to Wasabi for DOI: 100020
```

## Tests

### Unit test

The unit test checks custom-written `getAuthors` function in Dataset model class
which returns the authors of a dataset based on many-to-many mapping between
dataset and author tables via a junction `dataset_authors` table:
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/unit
```

There's also a unit test to check the ReadmeGenerator component class:
```
$ docker-compose run --rm tool ./vendor/bin/codecept run --debug tests/unit/components/ReadmeGeneratorTest.php
```

### Functional test

There is a functional test which checks the `actionCreate()` function in
`ReadmeController`.
```
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional

# Run single test
$ docker-compose run --rm tool ./vendor/bin/codecept run tests/functional/ReadmeCest.php
```

## Using readme generator tool on Bastion server

Before running the bastion playbook, execute the following command to install
the ansible-rclone role for Ansible in your development environment:
```
$ ansible-galaxy install -r ../../../infrastructure/requirements.yml
```

Running the bastion playbook will copy `createReadme.sh` script to the server:
```
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml
```

If changes are made to `createReadme.sh`, this part of the bastion playbook will
need to be executed again to copy the updated shell script to the server:
```
$ env OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES ansible-playbook -i ../../inventories bastion_playbook.yml --tags "readme_tool"
```

Instantiate and log into bastion server:
```
# Get public IP address for bastion server
$ terraform output
ec2_bastion_private_ip = "10.88.8.888"
ec2_bastion_public_ip = "88.888.888.888"

# Log into bastion server
$ ssh -i ~/.ssh/your-private-key.pem centos@88.888.888.888
```

Before executing the `createReadme` tool, get the existing values of the `file` table and `file_attributes` table:
```
# check the tables
[centos@ip-10-99-0-207 ~]$ psql -h $rds_instance_address -U gigadb -c 'select id, name, location, size from file where dataset_id = 200'
Password for user gigadb: 
  id   |                      name                       |                                                                   location                                                                    |   size    
-------+-------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------+-----------
 87517 | Diagram-ALL-FIELDS-Check-annotation.jpg         | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/Diagram-ALL-FIELDS-Check-annotation.jpg         |     55547
 87540 | SRAmetadb.zip                                   | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/SRAmetadb.zip                                   | 383892184
 87542 | Diagram-SRA-Study-Experiment-Joined-probing.jpg | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/Diagram-SRA-Study-Experiment-Joined-probing.jpg |     81717
 87516 | readme.txt                                      | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/readme.txt                                      |      2351
(4 rows)
[centos@ip-10-99-0-207 ~]$ psql -h $rds_instance_address -U gigadb -c 'select * from file_attributes where file_id = 87516'
Password for user gigadb: 
  id   | file_id | attribute_id |               value                | unit_id 
-------+---------+--------------+------------------------------------+---------
 17051 |   87516 |          605 | c60c299fdf375b28cd444e70f43fa398   | 
(1 row)
```

Using docker command to access tool:
```
$ docker run --rm -v /home/centos/readmeFiles:/app/readmeFiles registry.gitlab.com/$GITLAB_PROJECT/production_tool:$GIGADB_ENV /app/yii readme/create --doi 100142 --outdir /app/readmeFiles --bucketPath wasabi:gigadb-datasets/$GIGADB_ENV/pub/10.5524
```

Check the tables `file` and `file_attribbutes` that `name`, `location`, `size` and `value` have been updated.
```
[centos@ip-10-99-0-207 ~]$ psql -h rds-server-staging-ken.cjizsjwbxkxv.ap-northeast-2.rds.amazonaws.com -U gigadb -c 'select id, name, location, size from file where dataset_id = 200'
Password for user gigadb: 
  id   |                      name                       |                                                                   location                                                                    |   size    
-------+-------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------+-----------
 87516 | readme_100142.txt                               | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/staging/pub/10.5524/100001_101000/100142/readme_100142.txt                            |      1672
 87540 | SRAmetadb.zip                                   | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/SRAmetadb.zip                                   | 383892184
 87542 | Diagram-SRA-Study-Experiment-Joined-probing.jpg | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/Diagram-SRA-Study-Experiment-Joined-probing.jpg |     81717
 87517 | Diagram-ALL-FIELDS-Check-annotation.jpg         | https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100142/Diagram-ALL-FIELDS-Check-annotation.jpg         |     55547
(4 rows)
[centos@ip-10-99-0-207 ~]$ psql -h rds-server-staging-ken.cjizsjwbxkxv.ap-northeast-2.rds.amazonaws.com -U gigadb -c 'select * from file_attributes where file_id = 87516'
Password for user gigadb: 
  id   | file_id | attribute_id |              value               | unit_id 
-------+---------+--------------+----------------------------------+---------
 17051 |   87516 |          605 | 0fe8d1309283ffac54d782fd44434f9e | 
(1 row)

```

Check a readme file has been created:
```
$ head readmeFiles/readme_100142.txt 
[DOI] 10.5524/100142

[Title] Supporting scripts and data for "Investigation into the annotation of
protocol sequencing steps in the Sequence Read Archive".
```

Use shell script to run readme tool:
```
$ /usr/local/bin/createReadme --doi 100142 --outdir /app/readmeFiles
```

This time, you can check the log of this create readme file command:
```
$ more uploadLogs/readme_100142_20230901_080216.log 
2024/06/17 02:40:24 INFO  : Created readme file for DOI 100142 in /usr/local/bin/runtime/curators/readme_100142.txt
```

The createReadme.sh script can also be used to copy the newly created readme 
file into the Wasabi gigadb-datasets bucket. To test this in dry-run mode,
execute:
```
$ /usr/local/bin/createReadme --doi 100142 --outdir /app/readmeFiles --wasabi
```

And then check the rclone log:
```
[centos@ip-10-99-0-207 ~]$ more uploadLogs/readme_100142_20240617_030024.log 
2024/06/17 03:00:29 INFO  : Created readme file for DOI 100142 in /usr/local/bin/runtime/curators/readme_100142.txt
2024/06/17 03:00:30 NOTICE: readme_100142.txt: Skipped copy as --dry-run is set (size 1.640Ki)
2024/06/17 03:00:30 NOTICE: 
Transferred:        1.640 KiB / 1.640 KiB, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         1.0s

2024/06/17 03:00:30 INFO  : Executed: rclone copy --s3-no-check-bucket /home/centos/readmeFiles/readme_100142.txt wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100142/ --config /home/centos/.config/rclone/rclone.conf
 --dry-run --log-file /home/centos/uploadDir/readme_100142_20240617_030024.log --log-level INFO --stats-log-level DEBUG >> /home/centos/uploadDir/readme_100142_20240617_030024.log
2024/06/17 03:00:30 INFO  : Successfully copied file to Wasabi for DOI: 100142
```

If you look at the latest log file in the logs directory, you will see the
destination path that the readme file will be copied to which will be in the 
staging directory. You can deactivate dry-run mode using the --apply flag:
```
$ /usr/local/bin/createReadme --doi 100142 --outdir /app/readmeFiles --wasabi --apply
```

And the rclone log will be:
```
[centos@ip-10-99-0-207 ~]$ more uploadLogs/readme_100142_20240617_030758.log
2024/06/17 03:08:03 INFO  : Created readme file for DOI 100142 in /usr/local/bin/runtime/curators/readme_100142.txt
2024/06/17 03:08:03 INFO  : readme_100142.txt: Copied (replaced existing)
2024/06/17 03:08:03 INFO  : Executed: rclone copy --s3-no-check-bucket /home/centos/readmeFiles/readme_100142.txt wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100142/ --config /home/centos/.config/rclone/rclone.conf
 --log-file /home/centos/uploadDir/readme_100142_20240617_030758.log --log-level INFO --stats-log-level DEBUG >> /home/centos/uploadDir/readme_100142_20240617_030758.log
2024/06/17 03:08:03 INFO  : Successfully copied file to Wasabi for DOI: 100142
```

You can confirm that the presence of the new readme file in the 100142 directory
using the Wasabi web console by checking the gigadb-datasets/staging bucket.

There is a batch mode for the script which can be used by providing the 
`--batch` flag followed by a number to denote the number of datasets to be
processed. For example, to process DOIs 100141, 100142, 100143:
```
$ /usr/local/bin/createReadme --doi 100141 --outdir /app/readmeFiles --wasabi --batch 3
```

You will be able to see in the latest log file in the logs directory that 3
readme files have been created and copied into Wasabi in dry-run mode.

```
[centos@ip-10-99-0-207 ~]$ more uploadLogs/readme_100141_20240617_032006.log 
2024/06/17 03:20:12 INFO  : Created readme file for DOI 100141 in /usr/local/bin/runtime/curators/readme_100141.txt
2024/06/17 03:20:12 NOTICE: readme_100141.txt: Skipped copy as --dry-run is set (size 3.646Ki)
2024/06/17 03:20:12 NOTICE: 
Transferred:        3.646 KiB / 3.646 KiB, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.2s

2024/06/17 03:20:12 INFO  : Executed: rclone copy --s3-no-check-bucket /home/centos/readmeFiles/readme_100141.txt wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100141/ --config /home/centos/.config/rclone/rclone.conf
 --dry-run --log-file /home/centos/uploadDir/readme_100141_20240617_032006.log --log-level INFO --stats-log-level DEBUG >> /home/centos/uploadDir/readme_100141_20240617_032006.log
2024/06/17 03:20:12 INFO  : Successfully copied file to Wasabi for DOI: 100141
2024/06/17 03:20:16 INFO  : Created readme file for DOI 100142 in /usr/local/bin/runtime/curators/readme_100142.txt
2024/06/17 03:20:17 NOTICE: readme_100142.txt: Skipped copy as --dry-run is set (size 1.640Ki)
2024/06/17 03:20:17 NOTICE: 
Transferred:        1.640 KiB / 1.640 KiB, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.2s

2024/06/17 03:20:17 INFO  : Executed: rclone copy --s3-no-check-bucket /home/centos/readmeFiles/readme_100142.txt wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100142/ --config /home/centos/.config/rclone/rclone.conf
 --dry-run --log-file /home/centos/uploadDir/readme_100141_20240617_032006.log --log-level INFO --stats-log-level DEBUG >> /home/centos/uploadDir/readme_100141_20240617_032006.log
2024/06/17 03:20:17 INFO  : Successfully copied file to Wasabi for DOI: 100142
2024/06/17 03:20:22 INFO  : Created readme file for DOI 100143 in /usr/local/bin/runtime/curators/readme_100143.txt
2024/06/17 03:20:22 NOTICE: readme_100143.txt: Skipped copy as --dry-run is set (size 5.145Ki)
2024/06/17 03:20:22 NOTICE: 
Transferred:        5.145 KiB / 5.145 KiB, 100%, 0 B/s, ETA -
Transferred:            1 / 1, 100%
Elapsed time:         0.2s

2024/06/17 03:20:22 INFO  : Executed: rclone copy --s3-no-check-bucket /home/centos/readmeFiles/readme_100143.txt wasabi:gigadb-datasets/staging/pub/10.5524/100001_101000/100143/ --config /home/centos/.config/rclone/rclone.conf
 --dry-run --log-file /home/centos/uploadDir/readme_100141_20240617_032006.log --log-level INFO --stats-log-level DEBUG >> /home/centos/uploadDir/readme_100141_20240617_032006.log
2024/06/17 03:20:22 INFO  : Successfully copied file to Wasabi for DOI: 100143
[centos@ip-10-99-0-207 ~]$ 

```

To copy the readme file to the live data directory, use the `--use-live-data`
and `--apply` flags:
```
$ /usr/local/bin/createReadme --doi 100142 --outdir /app/readmeFiles --wasabi --use-live-data --apply
```

Now check the directory for dataset 100142 in relevant location in
gigadb-datasets/live bucket in Wasabi.

