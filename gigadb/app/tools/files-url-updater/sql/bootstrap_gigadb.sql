create role gigadb NOSUPERUSER CREATEDB CREATEROLE INHERIT LOGIN;
create database gigadb owner gigadb;
create database gigadb_test owner gigadb;