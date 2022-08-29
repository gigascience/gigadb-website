#!/usr/bin/env bash

set -exu

baseDir=${1:-"/sql"}

export PGPASSWORD=$DB_PG_PASSWORD
psql -U $DB_PG_USER -d $DB_PG_DATABASE -h $DB_PG_HOST -p 5432 < $baseDir/addConstraintsQuery.sql
psql -U $DB_PG_USER -d $DB_PG_DATABASE -h $DB_PG_HOST -p 5432 < $baseDir/addIndexQuery.sql
psql -U $DB_PG_USER -d $DB_PG_DATABASE -h $DB_PG_HOST -p 5432 < $baseDir/addTriggerQuery.sql
