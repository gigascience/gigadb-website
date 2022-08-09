#!/usr/bin/env bash

set -exu

backupFile=${1:-"/converted/gigadbv3.backup"}

export PGPASSWORD=$DB_PG_PASSWORD
psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c  "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname='gigadb';"
psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "drop database if exists $DB_PG_DATABASE"
psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "create database $DB_PG_DATABASE owner $DB_PG_USER"
psql -v ON_ERROR_STOP=1 -U $DB_PG_USER -h $DB_PG_HOST -p 5432 < $backupFile
psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "select count(*) from dataset"
psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "select identifier, publication_date from dataset where publication_date is not null order by publication_date desc limit 1"
psql -U $DB_PG_USER -d postgres -h $DB_PG_HOST -p 5432 -c "select * from tbl_migration"