CREATE TABLE ftp_user (name VARCHAR(20), password VARCHAR(20));
ALTER TABLE ftp_user OWNER TO gigadb;

insert into ftp_user values ('user1','gigadb1');
insert into ftp_user values ('user2','gigadb2');
insert into ftp_user values ('user3','gigadb3');
insert into ftp_user values ('user4','gigadb4');

REVOKE ALL ON TABLE ftp_user FROM PUBLIC;
REVOKE ALL ON TABLE ftp_user FROM gigadb;
GRANT ALL ON TABLE ftp_user TO gigadb;
GRANT ALL ON TABLE ftp_user TO PUBLIC;