#!/usr/bin/env bash

psql -h pg9_3 -U postgres --dbname gigadb_test -c "drop owned by gigadb;"
psql -h pg9_3 -U postgres --dbname gigadb_test -f sql/gigadb_tables.sql