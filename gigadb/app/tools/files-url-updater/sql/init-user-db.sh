#!/bin/bash
set -exu

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
	CREATE USER gigadb;
	CREATE DATABASE gigadb;
	GRANT ALL PRIVILEGES ON DATABASE gigadb TO gigadb;
EOSQL