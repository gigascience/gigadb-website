SELECT pg_terminate_backend(procpid) FROM pg_stat_activity WHERE datname='gigadb';
drop database gigadb ;
create database gigadb owner gigadb;
--\connect gigadb;