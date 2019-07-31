-- delete session of another users
SELECT pg_terminate_backend(pg_stat_activity.pid)
    FROM pg_stat_activity
    WHERE pg_stat_activity.datname = 'gigadb_test'
      AND pid <> pg_backend_pid();
drop database gigadb_test;
drop user test;
create user test with password 'test';
create database gigadb_test owner test;
-- grant ALL PRIVILEGES on DATABASE gigadb_test to test;