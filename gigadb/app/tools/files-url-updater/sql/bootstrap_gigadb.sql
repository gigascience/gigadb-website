create role gigadb NOSUPERUSER CREATEDB CREATEROLE INHERIT LOGIN;
create role gigadb_test LOGIN;
create database gigadb owner gigadb;
create database gigadb_test owner gigadb_test;