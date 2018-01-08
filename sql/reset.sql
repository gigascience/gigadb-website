SELECT pg_terminate_backend(procpid) FROM pg_stat_activity WHERE datname='gigadb';
--drop database gigadb ;
--create database gigadb owner gigadb;
\connect gigadb;

TRUNCATE TABLE gigadb_user CASCADE;

COPY gigadb_user (id, email, password, first_name, last_name, affiliation, role, is_activated, newsletter, previous_newsletter_state, facebook_id, twitter_id, linkedin_id, google_id, username, orcid_id, preferred_link) FROM stdin;
344	admin@gigadb.org	5a4f75053077a32e681f81daa8792f95	Joe	Bloggs	BGI	admin	t	f	t	\N	\N	\N	\N	test@gigadb.org	\N	EBI
345	user@gigadb.org	5a4f75053077a32e681f81daa8792f95	John	Smith	BGI	user	t	f	t	\N	\N	\N	\N	user@gigadb.org	\N	EBI
\.
