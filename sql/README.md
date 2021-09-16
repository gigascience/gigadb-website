# Files

The GigaDB website provides information about datasets associated
with scientific papers published in *[GigaScience](http://gigascience.biomedcentral.com)*
journal. The dataset information is stored in a [PostgreSQL](http://www.postgresql.org)
database.

This directory contains SQL dumps of the GigaDB PostgreSQL database
which can be used to instantiate a version which is required for GigaDB
development.

| Filename            | Description                                  |
| ------------------- | ---------------------------------------------|
| gigadb_tables.sql   | Tables only, no content                      |
| gigadb_testdata.sql | Tables and test data                         |
| gigadb_schema.svg   | An SVG diagram of the gigadb database schema |

## How to use sql files in GigaDB virtual machines

A *.sql file is used to instantiate a database during the Chef Solo
provisioning process. Decide on the sql file you would like to use
and add it to the `default[:gigadb][:db][:sql_script]` attribute
prefixed with `/vagrant/sql/` in the
`chef/site-cookbooks/gigadb/attributes/default.rb` file. For example:

```
default[:gigadb][:db][:sql_script] = '/vagrant/sql/gigadb_tables.sql'
```

## Database schema

To view the database schema for GigaDB, open the gigadb_schema.svg file
in a web browser.

## Generating a database dump

If you need to create a SQL dump of the database, you can use the
`pg_dump` tool in the GigaDB virtual machine:

```bash
$ pg_dump -U gigadb -h localhost -W -F plain gigadb > /vagrant/sql/gigadb_dump.sql
```

## Using a *.backup file to load data

If you have been provided with a *.backup file as a database dump then these
commands will load the data into the PostgreSQL database:

```bash
sudo -u postgres /usr/bin/psql -c 'drop database gigadb'
sudo -u postgres /usr/bin/psql -c 'create database gigadb owner gigadb'
sudo -u postgres /usr/bin/psql -f gigadbv3_20170815_plant.backup> gigadb 
```

## Convert production database backup to modern version of PostgreSQL [#731](https://github.com/gigascience/gigadb-website/issues/731)
The `PostgreSQL` version for production database is `9.1.17`, while that for latest development work is `9.6.22`,
So, there is a need to upgrade production database to `higher version`.

Running this command would convert the production database version to `9.3+`.
```bash
cd /gigadb-website

# Spin up all gigaDB containers
./up.sh

# Go to files-url-updater dir
cd gigadb/app/tools/files-url-updater/ 

# Spin up pg9_3 container
docker-compose up -d pg9_3

# Configure files-url-updater
cp config/params.php.example config/params.php
# Then update the ftp credentials in params.php
'ftp' => [ # connection details to the ftp server where to download production backup from
        "host" => "", # host for the ftp server
        "username" => "", # ftp username to use to login to ftp server
        "password" => "", # ftp password to use to login to ftp server
    ],

# Run the script
cd /gigadb-website
./ops/scripts/convert_production_db_to_latest_ver.sh
```

### How does the script work
Download the production database and load it into postgreSQL server using `files-url-updater` tool. You could specify the date which database backup you want to use
by including `--date` parameter. By default, this command will download and restore the latest production database.
```bash
cd gigadb/app/tools/files-url-updater/

# Downlaod and restore the latest production database 
docker-compose run --rm updater ./yii dataset-files/download-restore-backup --latest

# Export production data as text
docker-compose run --rm updater pg_dump -h pg9_3 -U gigadb  --clean --create --schema=public --no-privileges --no-tablespaces gigadb -f sql/gigadbv3_"$latest"_v"$version".backup
```
For a detailed usage information, please
go to [here](https://github.com/rija/gigadb-website/tree/files-url-updater-%23629/gigadb/app/tools/files-url-updater)