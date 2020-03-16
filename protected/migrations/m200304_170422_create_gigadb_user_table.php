<?php

class m200304_170422_create_gigadb_user_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE gigadb_user (
                id integer NOT NULL,
                email character varying(64) NOT NULL,
                password character varying(128) NOT NULL,
                first_name character varying(100) NOT NULL,
                last_name character varying(100) NOT NULL,
                affiliation character varying(200),
                role character varying(30) DEFAULT \'user\'::character varying NOT NULL,
                is_activated boolean DEFAULT false NOT NULL,
                newsletter boolean DEFAULT true NOT NULL,
                previous_newsletter_state boolean DEFAULT false NOT NULL,
                facebook_id text,
                twitter_id text,
                linkedin_id text,
                google_id text,
                username text NOT NULL,
                orcid_id text,
                preferred_link character varying(128) DEFAULT \'EBI\'::character varying)'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE gigadb_user_id_seq 
                START WITH 20 
                INCREMENT BY 1 
                NO MINVALUE 
                NO MAXVALUE 
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE gigadb_user_id_seq 
                OWNED BY gigadb_user.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY gigadb_user 
                ALTER COLUMN id SET DEFAULT nextval(\'gigadb_user_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY gigadb_user
                ADD CONSTRAINT email_unique UNIQUE (email);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY gigadb_user
                ADD CONSTRAINT gigadb_user_facebook_id_key UNIQUE (facebook_id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY gigadb_user
                ADD CONSTRAINT gigadb_user_google_id_key UNIQUE (google_id);'
        );

        $sql_altertab5 = sprintf(
            'ALTER TABLE ONLY gigadb_user
                ADD CONSTRAINT gigadb_user_linked_id_key UNIQUE (linkedin_id);'
        );

        $sql_altertab6 = sprintf(
            'ALTER TABLE ONLY gigadb_user
                ADD CONSTRAINT gigadb_user_orcid_id_key UNIQUE (orcid_id);'
        );

        $sql_altertab7 = sprintf(
            'ALTER TABLE ONLY gigadb_user
                ADD CONSTRAINT gigadb_user_pkey PRIMARY KEY (id);'
        );

        $sql_altertab8 = sprintf(
            'ALTER TABLE ONLY gigadb_user
                ADD CONSTRAINT gigadb_user_twitter_id_key UNIQUE (twitter_id);'
        );

        $sql_altertab9 = sprintf(
            'ALTER TABLE ONLY gigadb_user
                ADD CONSTRAINT gigadb_user_username_key UNIQUE (username);'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4, $sql_altertab5, $sql_altertab6, $sql_altertab7, $sql_altertab8, $sql_altertab9);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table
        $this->insert('gigadb_user', array(
            'id' => '1',
            'email' => 'admin@gigadb.org',
            'password' => md5("vagrant"),
            'first_name' => 'admin',
            'last_name' => 'user',
            'affiliation' => 'gigascience',
            'role' => 'admin',
            'is_activated' => 'true',
            'newsletter' => 'true',
            'previous_newsletter_state' => 'true',
            'username' => 'test+user@gigasciencejournal.com'
        ));
        $this->insert('gigadb_user', array(
            'id' => '2',
            'email' => 'user@gigadb.org',
            'password' => md5("vagrant"),
            'first_name' => 'test',
            'last_name' => 'user',
            'affiliation' => 'gigascience',
            'is_activated' => 'true',
            'newsletter' => 'true',
            'previous_newsletter_state' => 'true',
            'username' => 'test+admin@gigasciencejournal.com'
        ));
        $this->insert('gigadb_user', array(
            'id' => '3',
            'email' => 'test+gigadb_user1@gigasciencejournal.com',
            'password' => '',
            'first_name' => 'test',
            'last_name' => 'gigadb_user1',
            'affiliation' => 'BGI',
            'is_activated' => 'true',
            'newsletter' => 'true',
            'previous_newsletter_state' => 'true',
            'username' => 'test+gigadb_user1@gigasciencejournal.com'
        ));
        $this->insert('gigadb_user', array(
            'id' => '8',
            'email' => 'test+gigadb_user2@gigasciencejournal.com',
            'password' => '',
            'first_name' => 'test',
            'last_name' => 'gigadb_user2',
            'affiliation' => 'BGI',
            'is_activated' => 'false',
            'newsletter' => 'true',
            'previous_newsletter_state' => 'true',
            'username' => 'test+gigadb_user2@gigasciencejournal.com'
        ));
        $this->insert('gigadb_user', array(
            'id' => '17',
            'email' => 'test+gigadb_user3@gigasciencejournal.com',
            'password' => '',
            'first_name' => 'test',
            'last_name' => 'gigadb_user3',
            'affiliation' => 'BGI',
            'is_activated' => 'false',
            'newsletter' => 'true',
            'previous_newsletter_state' => 'true',
            'username' => 'test+gigadb_user3@gigasciencejournal.com'
        ));
    }

    public function safeDown()
    {
        Yii::app()->db->createCommand('DROP SEQUENCE gigadb_user_id_seq CASCADE;')->execute();
        $this->dropTable('gigadb_user');
    }
}
