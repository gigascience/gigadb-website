# ExceltoGigaDB

## Preparation

Your dev environment GigaDB website needs to be running so execute the command
below in the root directory of your `gigadb-website` repo:
```
$ ./up.sh
```
As part of the `./up.sh` process, new data will be added into the `species` and
`external_link_type` tables from their csv files in `data/dev` directory which
are required  for running the Excel upload tool.

Next, download consultant's tool as a zip file into the 
`excel-spreadsheet-uploader` directory:
```
# Go to tool directory
$ cd gigadb/app/tools/excel-spreadsheet-uploader
$ curl -L -O https://github.com/gigascience/ExceltoGigaDB/archive/develop.zip
```

Unpack contents in zip file:
```
# -k stops README.md from being over-written
$ bsdtar -k --strip-components=1 -xvf develop.zip
```

## Tool execution

There is an example Excel spreadsheet file `100679newversion.xls` in the 
`uploadDir` directory. The metadata provides information about an Eucalytpus 
dataset which can be uploaded into your `dev` GigaDB using the commands below:
```
$ docker-compose run --rm uploader ./run.sh
```

The tool will generated `javac.log` and `java.log` files which provide 
information about the upload process.

If the tool as successfully executed then you can see the uploaded dataset in 
the GigaDB website. Log into your local GigaDB website with the 
`admin@gigadb.org` account and then go to http://gigadb.gigasciencejournal.com:9170/adminDataset/update/id/701. You should see the dataset admin page for
the new `Dataset 100679`. Also, checkout the `dataset` table in the PostgreSQL
database.

---

Convert excel spreadsheet to sql file into GigaDB database

The folder uploadDir stores spreadsheet which prepare to import into database.

The folder dataDir stores all spreadsheets which already imported into database.

The folder logFile stores all datasets import log including error log (if you want to debug it).

The folder sqlFile stores all datasets import sql files.

The folder configuration stores all parameters including database setting, validation setting, file-format setting etc.

The lib stores all jar packages which used in this project. 

# How to run it

## step 1

`/configuration/setting.xml` set the databaseUrl, databaseUsername, databasePassword

## step 2

Make sure `/uploadDir` folder has the spreadsheet. e.g. 100670newversion.xls

## step 3

Run `/src/Main.java`

## step 4

Check the log file and dataDir to see whether it was successful uploaded.




