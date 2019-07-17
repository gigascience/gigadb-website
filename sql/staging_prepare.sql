drop database gigadb_test;
drop user test;
create user test with password 'test';
create database gigadb_test owner test;
-- grant ALL PRIVILEGES on DATABASE gigadb_test to test;