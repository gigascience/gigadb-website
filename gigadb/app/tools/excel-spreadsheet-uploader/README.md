# ExceltoGigaDB

```
# Download consultant's tool
curl -L -O https://github.com/gigascience/ExceltoGigaDB/archive/develop.zip
# Unpack contents
bsdtar --strip-components=1 -xvf develop.zip
# Execute tool
docker-compose run --rm uploader ./run.sh
```

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




