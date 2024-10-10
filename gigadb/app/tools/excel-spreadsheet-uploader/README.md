# ExceltoGigaDB

## Preparation

Your dev environment GigaDB website needs to be running so execute the command
below in the root directory of your `gigadb-website` repo:
```
$ ./up.sh
```
As part of the `./up.sh` process, new data will be added into the `species` and
`external_link_type` tables from their csv files in `data/dev` (and 
``data/production_like`` for the latter) directory which are required for 
running the Excel upload tool.

Next, we need to setup the tool by running the setup script that will configure 
database connection and download the java source code of the Excel2DB tool.

```
$ ./setup.sh
```

>Note: You only need to run that script once per environment (unless such environment is re-created)
> but the script is idempotent anyway

## Tool execution

There is an example Excel spreadsheet file `100679newversion.xls` in the 
`uploadDir` directory. The metadata provides information about an Eucalytpus 
dataset which can be uploaded into your `dev` GigaDB using the commands below:
```
$ ./execute.sh
```

The tool will generate `javac.log` and `java.log` files which provide 
information about the upload process.

If the tool as successfully executed then you can see the uploaded dataset in 
the GigaDB website. Log into your local GigaDB website with the 
`admin@gigadb.org` account and then go to http://gigadb.gigasciencejournal.com:9170/adminDataset/update/id/701.
You should see the dataset admin page for the new `Dataset 100679`. Also, 
checkout the `dataset`, ``file``, and ``sample`` tables (and their connecting 
tables) in the PostgreSQL database.

Running `execute.sh` will only ingest information relating to the study, samples
and files that are contained in the Excel spreadsheet into the database. To add
md5 checksum values and file size information, the `postUpload.sh` script has to
be executed. It is possible to run the tools used in the post upload script on
their own. For example, to update files with md5 checksum values:
```
$ pwd
/path/to/gigadb-website
$ docker-compose run --rm  test ./protected/yiic files updateMD5FileAttributes --doi=100006
Saved md5 file attribute with id: 10674
Saved md5 file attribute with id: 10672
Saved md5 file attribute with id: 10673
Saved md5 file attribute with id: 10671
Saved md5 file attribute with id: 10670
Saved md5 file attribute with id: 10669
Saved md5 file attribute with id: 10675
```

Check md5 values have been updated for dataset 100006:
```
$ docker-compose run --rm test psql -h database -p 5432 -U gigadb gigadb -c "select file.name, file_attributes.value from file, file_attributes where file.dataset_id=8 and file_attributes.attribute_id=605 and file.id = file_attributes.file_id;"
                  name                  |              value               
----------------------------------------+----------------------------------
 Pygoscelis_adeliae.RepeatMasker.out.gz | 5afc9d8348bf4b52ee6e9c2bae9fd542
 Pygoscelis_adeliae.cds.gz              | bd9bed43475eaa22b6ab62b9fb7a3909
 Pygoscelis_adeliae.fa.gz               | 43b35c4e828bed20dbb071d2c5a40f17
 Pygoscelis_adeliae.gff.gz              | 47b8f47ca31cfd06d5ad62ceceb99860
 Pygoscelis_adeliae.pep.gz              | 23c3241e6bc362d659a4b589c8d9e01c
 readme.txt                             | 88888888888888888888888888888888
 Pygoscelis_adeliae.scaf.fa.gz          | 55c764721558086197bfbd663e1567a6
(7 rows)
```

To test update of file sizes, documentation is available in [files-metadata-console/README.md](../files-metadata-console/README.md).

## Remote execution

To load the spreadsheet in GigaDB instance deployed on the cloud, you need to:
1. Connect with ssh to bastion host of the desired environment
2. Change directory to ``gigadb-website-develop/gigadb/app/tools/excel-spreadsheet-uploader/``
3. Ensure the spreadsheet you want to process is in the ``uploadDir`` directory 
   (see below)
4. Run the ``datasetUpload.sh`` script as described above

>Note: the setup script is already run when the bastion playbook was executed to provision that environment

## How to upload a dataset spreadsheet to a bastion server

1. Ensure the spreadsheet file is present in the ``gigadb/app/tools/excel-spreadsheet-uploader/uploadDir`` 
   directory of your local environment.
2. From ``ops/infrastructure/envs/staging``, run the following Ansible command: 

```
ansible-playbook -i ../../inventories bastion_playbook.yml --tags "spreadsheet" 
```

That will synchronise the bastion's version of ``uploadDir`` with the local one.

>Note: alternatively you could also use ``scp`` to copy the local file to the bastion
