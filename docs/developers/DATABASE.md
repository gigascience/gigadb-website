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
with GigaDB datasets from database tables. To use the `export_csv.sh` script, we
need to ensure that the following variables have been configured in the .secrets 
file:
```
EXPORT_CSV_GIGADB_DB=
EXPORT_CSV_GIGADB_HOST=
EXPORT_CSV_GIGADB_USER=
EXPORT_CSV_GIGADB_PASSWORD=
```

We can now run the `export_csv.sh` script through the `test` service container 
making use of its `psql` client:
```
$ docker-compose run --rm test data/scripts/export_csv.sh -i "8 22 144 200" -o custom
```

The identifiers are provided to `export_csv.sh` as a string in a command line
argument using the `-i` flag. Other arguments which are required include `-o` 
an output directory where CSV will be saved to, and `-d` the name of the
database for querying. 

## Converting CSV files into Yii migration scripts

The CSV files must be converted to Yii migration scripts before the data can be
uploaded into a database. This is achieved using a `setup_devdb.sh` shell script 
as follows:
```
$ ops/scripts/setup_devdb.sh
```

The shell script executes a JavaScript program called `csv_yii_migration.js` 
which will convert the `data/dev` directory containing CSV files into Yii 
migration scripts in the `protected/migrations/data/dev` directory. Next, this 
shell script will run a series of Yii migration scripts to create a new dev
database containing data for development work. 
