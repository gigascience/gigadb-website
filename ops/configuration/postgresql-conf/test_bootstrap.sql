CREATE USER test WITH PASSWORD 'test';
CREATE DATABASE gigadb_test WITH TEMPLATE gigadb OWNER test;
\connect gigadb_test;
COPY gigadb_user (id, email, password, first_name, last_name, affiliation, role, is_activated, newsletter, previous_newsletter_state, facebook_id, twitter_id, linkedin_id, google_id, username, orcid_id, preferred_link) FROM stdin WITH DELIMITER '|';
346|author@gigadb.org|$argon2i$v=19$m=32768,t=4,p=1$A9YMTrvwt+JOgcmbo2kdQQ$rwN+pPSr1tB+6dAzPeeT73dB0Lhf/u1X6Cwvbrf75Yo|Anna|Chronic|BGI|user|t|f|t|\N|\N|\N|\N|author@gigadb.org|\N|EBI
347|new@gigadb.org|$argon2i$v=19$m=32768,t=4,p=1$A9YMTrvwt+JOgcmbo2kdQQ$rwN+pPSr1tB+6dAzPeeT73dB0Lhf/u1X6Cwvbrf75Yo|Orga|Nigram|BGI|user|f|f|t|\N|\N|\N|\N|new@gigadb.org|\N|EBI
348|social@gigadb.org|doh|Hale|Ktric|BGI|user|t|f|t|23545234|\N|\N|\N|social@gigadb.org|\N|EBI
