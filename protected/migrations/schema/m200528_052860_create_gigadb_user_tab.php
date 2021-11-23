<?php

class m200528_052860_create_gigadb_user_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS gigadb_user (
            id integer NOT NULL,
            email character varying(64) NOT NULL,
            password character varying(128) NOT NULL,
            first_name character varying(100) NOT NULL,
            last_name character varying(100) NOT NULL,
            affiliation character varying(200),
            role character varying(30) DEFAULT 'user'::character varying NOT NULL,
            is_activated boolean DEFAULT false NOT NULL,
            newsletter boolean DEFAULT true NOT NULL,
            previous_newsletter_state boolean DEFAULT false NOT NULL,
            facebook_id text,
            twitter_id text,
            linkedin_id text,
            google_id text,
            username text NOT NULL,
            orcid_id text,
            preferred_link character varying(128) DEFAULT 'EBI'::character varying);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS gigadb_user_id_seq 
            START WITH 20 
            INCREMENT BY 1 
            NO MINVALUE 
            NO MAXVALUE 
            CACHE 1;");

        $this->execute("ALTER SEQUENCE gigadb_user_id_seq 
            OWNED BY gigadb_user.id;");

        $this->execute("ALTER TABLE ONLY gigadb_user 
            ALTER COLUMN id SET DEFAULT nextval('gigadb_user_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY gigadb_user
            ADD CONSTRAINT email_unique UNIQUE (email);");

        $this->execute("ALTER TABLE ONLY gigadb_user
            ADD CONSTRAINT gigadb_user_facebook_id_key UNIQUE (facebook_id);");

        $this->execute("ALTER TABLE ONLY gigadb_user
            ADD CONSTRAINT gigadb_user_google_id_key UNIQUE (google_id);");

        $this->execute("ALTER TABLE ONLY gigadb_user
            ADD CONSTRAINT gigadb_user_linked_id_key UNIQUE (linkedin_id);");

        $this->execute("ALTER TABLE ONLY gigadb_user
            ADD CONSTRAINT gigadb_user_orcid_id_key UNIQUE (orcid_id);");

        $this->execute("ALTER TABLE ONLY gigadb_user
            ADD CONSTRAINT gigadb_user_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY gigadb_user
            ADD CONSTRAINT gigadb_user_twitter_id_key UNIQUE (twitter_id);");

        $this->execute("ALTER TABLE ONLY gigadb_user
            ADD CONSTRAINT gigadb_user_username_key UNIQUE (username);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE gigadb_user_id_seq CASCADE;");
        $this->dropTable('gigadb_user');
    }
}
