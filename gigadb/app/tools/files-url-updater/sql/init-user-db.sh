#!/bin/bash
set -exu

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
  create role gigadb NOSUPERUSER LOGIN;
  create role gigadb_test NOSUPERUSER LOGIN;
  create database gigadb owner gigadb;
  create database gigadb_test owner gigadb_test;
EOSQL