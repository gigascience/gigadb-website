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


## Remote execution

To load the spreadsheet in GigaDB instance deployed on the cloud, you need to:
1. Connect with ssh to bastion host of the desired environment
2. Change directory to ``gigadb-website-develop/gigadb/app/tools/excel-spreadsheet-uploader/``
3. Ensure the spreadsheet you want to process is in the ``uploadDir`` directory 
   (see below)
4. Run the ``execute.sh`` script as described above

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

## Usage notes for uploading Excel spreadsheets

### File Attributes

Files annotated with two or more file attributes can cause problems with upload
process. You may find that the key value pairs of the second file attribute and 
those thereafter will be concatenated into the value of the first file 
attribute. Resolution of this problem will involve manual creation of file 
attributes using the GigaDB admin interface.

### Dataset Types

A dataset needs to be annotated with one or more of the following valid dataset
types: Genomic, Imaging, Software, Transcriptomic, Bioinformatics, Workflow, 
Metagenomic, Neuroscience, Epigenomic, Proteomic, Metadata, Genome-Mapping, 
Metabolomic, Phenotyping, Network-Analysis, Ecology, Metabarcoding, 
Electrophysiology, Virtual-Machine, ElectroEncephaloGraphy(EEG), Data-Mining, 
Lipidomic, Hardware, Climate. If a dataset has been annotated with another term
then the upload will fail.

If one of these valid dataset types does not exist in the `type` database table
then the upload tool will create it.

### Sample Tab in Spreadsheet

There are comment and description rows in the Sample tab in the Excel
spreadsheet. These rows need to be deleted for a successful upload of the Excel
file.

### Funding information

Funding information needs to be formatted as follows:
```
University of Hawaii Cancer Center and V Foundation,V Scholar Award, ,L Wu;
National Cancer Institute, ,R01CA263494,C Wu;
National Cancer Institute, ,R01CA263494,L Wu;
```

If a column value is empty then this needs to be represented as a single space
character.

N.B. Funding information needs to be provided as a single line. Therefore, the
above funding information should look like this as the value of the 
`funding_information` field in the Study tab:
```
University of Hawaii Cancer Center and V Foundation,V Scholar Award, ,L Wu;National Cancer Institute, ,R01CA263494,C Wu;National Cancer Institute, ,R01CA263494,L Wu;
```
