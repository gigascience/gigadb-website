drop database gigadb_test;
drop user test;
create user test with password 'test';
create database gigadb_test owner test;
-- grant ALL PRIVILEGES on DATABASE gigadb_test to test;
\connect gigadb_test;
\i /var/www/sql/gigadb_tables.sql
COPY gigadb_user (id, email, password, first_name, last_name, affiliation, role, is_activated, newsletter, previous_newsletter_state, facebook_id, twitter_id, linkedin_id, google_id, username, orcid_id, preferred_link) FROM stdin;
344	admin@gigadb.org	5a4f75053077a32e681f81daa8792f95	Joe	Bloggs	BGI	admin	t	f	t	\N	\N	\N	\N	test@gigadb.org	\N	EBI
345	user@gigadb.org	5a4f75053077a32e681f81daa8792f95	John	Smith	BGI	user	t	f	t	\N	\N	\N	\N	user@gigadb.org	\N	EBI
\.
