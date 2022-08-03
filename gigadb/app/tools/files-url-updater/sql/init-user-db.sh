#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
	CREATE USER gigab;
	CREATE DATABASE gigab;
	GRANT ALL PRIVILEGES ON DATABASE gigadb TO gigadb;
EOSQL