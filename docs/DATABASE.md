# Database setup

Data for deploying GigaDB's PostgreSQL database is now kept in a collection of
CSV files which are located in the `gigadb-website/data` directory. For example,
the CSV files to deploy a database for development work are in `data/dev`:
```
$ pwd
/path/to/gigadb-website/data/dev
$ ls -lh
alternative_identifiers.csv dataset_funder.csv          extdb.csv                   file_relationship.csv       manuscript.csv              rss_message.csv             type.csv
attribute.csv               dataset_log.csv             external_link.csv           file_sample.csv             news.csv                    sample.csv                  unit.csv
author.csv                  dataset_project.csv         external_link_type.csv      file_type.csv               prefix.csv                  sample_attribute.csv
curation_log.csv            dataset_sample.csv          file.csv                    funder_name.csv             project.csv                 sample_experiment.csv
dataset.csv                 dataset_type.csv            file_attributes.csv         gigadb_user.csv             publisher.csv               sample_rel.csv
dataset_attributes.csv      exp_attributes.csv          file_experiment.csv         image.csv                   relation.csv                search.csv
dataset_author.csv          experiment.csv              file_format.csv             link.csv                    relationship.csv            species.csv
```

Each file is named after the PostgreSQL table whose data can be found in CSV
format in the file.

## Creating CSV files

These CSV files have been created using 
`gigadb-website/data/scripts/export_csv.sh` which queries a GigaDB PostgreSQL 
database for data corresponding to a list of internal dataset ids. These 
identifiers are used in a list of SQL SELECT commands to export data associated 
with GigaDB datasets from database tables.
```
$ pwd
/path/to/gigadb-website
# Execute script to export CSV data
$ data/scripts/export_csv.sh -v 6 -i "8 144 200 268" -o dev -d gigadbv3_20200210
```
 The identifiers are provided to `export_csv.sh` as a string in a command line
 argument using the `-i` flag. Other arguments which are required include `-o` 
 an output directory where CSV will be saved to, and `-d` the name of the
 database for querying. 

## Converting CSV files into Yii migration scripts

The CSV files must be converted to Yii migration scripts before the data can be
uploaded into a database. However, this is done automatically done as part of 
the config process:
```
$ docker-compose run --rm config            # generate the configuration using variables in .env, GitLab, then exit
```

This process executes `generate_config.sh` which now includes a new step:
```
node /var/www/ops/scripts/csv_yii_migration.js
```

A JavaScript program called `csv_yii_migration.js` is executed which will 
convert a directory containing CSV files into Yii migration scripts. This 
directory is specified by the `TARGET_DIR` variable in the `.env` file and has a 
default value of `dev` which means that the CSV files in 
`gigadb-website/data/dev` will be transformed into Yii migration scripts and 
these will be located in the `gigadb-website/protected/migrations/data/dev`
directory.

## Running database migrations

Now that Yii migration scripts are available, a database can be deployed for the
GigaDB website. This is achieved with two commands:
```
# Create schema tables
$ docker-compose run --rm  application ./protected/yiic migrate --migrationPath=application.migrations.schema --interactive=0
# Upload data into tables
$ docker-compose run --rm  application ./protected/yiic migrate --migrationPath=application.migrations.data.dev --interactive=0
```

The first command will create the schema tables for GigaDB's PostgreSQL 
database using the migration scripts in `gigadb-website/protected/migrations/schema`.
The second command will upload data into the tables using migration scripts that
were created as part of `docker-compose run --rm config` execution.


## Notes

The local dev and production deployments of the GigaDB application have 
discrepancies in their PostgreSQL database schemas. These differences need to be
removed for development work. Rija suggested using Yii database migrations for
instantiating a dev PostgreSQL database.

--

Postgresql can import and export CSV natively so why not exporting the data dump as a collection of CSV files?

Then write some console command that uses phpspreadsheet and Faker's Person provider to replace name and email of authors and users before creating migration scripts or fixture files.

It didn't occurred to me of doing the last step in an interactive tool, but so it seems that PHP Storm can load the CSV and apply a custom extractor that will do what I describe in the last step.

I think we do update test data occassionally but not regularly, so an interactive tool could be enough. Especially, that the context we need to think of test data is when we code the automated tests which make the IDE the ideal place to do the processing.

Also, the data editor in PHPStorm allow  manual edit of the spreadsheets if needed.

https://www.jetbrains.com/help/phpstorm/working-with-the-data-editor.html

Following up that line of thiniking, I was wondering whether it's preferable to maintain test data as CSV only in git.  We have some tests (acceptance) that expect the database to be in certain state (migrations) and other tests that load data fixtures for their test data (unit and functional).

In both case, the test data needs to be  kept up-to-date. So if we keep CSV as the main source of truth, we can export to both fixtures for unit and functional tests, and migrations for acceptance tests in a particular environment.

Going further, we could also keep the reference data as CSV and export them as migrations, fixtures and other formats:

At the moment, the Vue.js application in File Upload Wizard needs to consume a json file for the list of formats and data types.  We could have a third custom extractor that would transform the reference data CSVs into JSON files. (at the moment, they are created with console commands, but an interactive data editor sounds preferable for the same reason stated above)


Re custom data extractor: Javascript would be preferable to Groovy as in theory we can tap (to confirm) the vast library from NPM. (e.g: the javascript Faker is far more richer than it’s PHP counterpart) and we already use Javascript in the codebase.

### Migration script creation

```
# Log into test container
docker-compose run --rm test bash
$ Change directory
cd /var/www/protected
# Create migration
./yiic migrate create create_dataset_table
```

N.B. The migration PHP files will use tab, and not space characters for indents.

### Redundant files

* The ops/configuration/postgresql-conf directory contains a SQL file 
`empty_bootstrap.sql` for creating an empty gigadb database that contains no 
tables.

* The protected/migrations directory contains 4 migrations:

1. m181108_105104_widen_user_password_column.php
2. m181128_090415_setup_curation_log_sequence.php
3. m190527_025615_dataset_upload_status.php
4. m191015_013705_alter_curation_comments.php

These PHP files should be deleted since they will all be redundant with this
new database setup.

### Data privacy

Need to be aware of privacy of email addresses in test data, e.g. gigadb_user table.




