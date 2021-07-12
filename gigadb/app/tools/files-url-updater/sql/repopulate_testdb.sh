#!/usr/bin/env bash

psql -h pg9_3 -U postgres --dbname gigadb_test -c "drop owned by gigadb_test;"
psql -h pg9_3 -U postgres --dbname gigadb_test -f sql/gigadb_tables.sql
psql -h pg9_3 -U postgres --dbname gigadb_test -c "grant all privileges on all tables in schema public to gigadb_test;"
psql -h pg9_3 -U postgres --dbname gigadb_test -c "grant all privileges on all sequences in schema public to gigadb_test;"
psql -h pg9_3 -U postgres --dbname gigadb_test -c "grant all privileges on all functions in schema public to gigadb_test;"