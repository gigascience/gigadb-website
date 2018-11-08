drop database gigadb_test;
drop user test;
create user test with password 'test';
create database gigadb_test owner test;
-- grant ALL PRIVILEGES on DATABASE gigadb_test to test;
\connect gigadb_test;
\i /var/www/ops/configuration/postgresql-conf/test_bootstrap.sql
COPY gigadb_user (id, email, password, first_name, last_name, affiliation, role, is_activated, newsletter, previous_newsletter_state, facebook_id, twitter_id, linkedin_id, google_id, username, orcid_id, preferred_link) FROM stdin;
344	admin@gigadb.org	5a4f75053077a32e681f81daa8792f95	Joe	Bloggs	BGI	admin	t	f	t	\N	\N	\N	\N	test@gigadb.org	\N	EBI
345	user@gigadb.org	5a4f75053077a32e681f81daa8792f95	John	Smith	BGI	user	t	f	t	\N	\N	\N	\N	user@gigadb.org	\N	EBI
346	author@gigadb.org	$argon2i$v=19$m=32768,t=4,p=1$A9YMTrvwt+JOgcmbo2kdQQ$rwN+pPSr1tB+6dAzPeeT73dB0Lhf/u1X6Cwvbrf75Yo	Anna	Chronic	BGI	user	t	f	t	\N	\N	\N	\N	author@gigadb.org	\N	EBI
347	new@gigadb.org	$argon2i$v=19$m=32768,t=4,p=1$A9YMTrvwt+JOgcmbo2kdQQ$rwN+pPSr1tB+6dAzPeeT73dB0Lhf/u1X6Cwvbrf75Yo	Orga	Nigram	BGI	user	f	f	t	\N	\N	\N	\N	new@gigadb.org	\N	EBI
348	social@gigadb.org	doh	Hale	Ktric	BGI	user	t	f	t	23545234	\N	\N	\N	social@gigadb.org	\N	EBI
\.
