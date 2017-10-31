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