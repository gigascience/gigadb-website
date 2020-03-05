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
                password character varying(64) NOT NULL,
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
                START WITH 1 
                INCREMENT BY 1 
                NO MINVALUE 
                NO MAXVALUE 
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE gigadb_user_id_seq 
                OWNED BY gigadb_user.id;'
        );

        $sql_altertab = sprintf(
            'ALTER TABLE ONLY gigadb_user 
                ALTER COLUMN id SET DEFAULT nextval(\'gigadb_user_id_seq\'::regclass);'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table
        $this->insert('gigadb_user', array(
            'id' => '3',
            'email' => 'test+gigadb_user@gigasciencejournal.com',
            'password' => '',
            'first_name' => 'test',
            'last_name' => 'gigadb_user',
            'affiliation' => 'BGI',
            'is_activated' => 'false',
            'newsletter' => 'true',
            'previous_newsletter_state' => 'true',
            'username' => 'test+gigadb_user@gigasciencejournal.com'
        ));
        $this->insert('gigadb_user', array(
            'id' => '8',
            'email' => 'test+gigadb_user@gigasciencejournal.com',
            'password' => '',
            'first_name' => 'test',
            'last_name' => 'gigadb_user',
            'affiliation' => 'BGI',
            'is_activated' => 'false',
            'newsletter' => 'true',
            'previous_newsletter_state' => 'true',
            'username' => 'test+gigadb_user@gigasciencejournal.com'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('gigadb_user');
        Yii::app()->db->createCommand('DROP SEQUENCE gigadb_user_id_seq;')->execute();
    }
}
