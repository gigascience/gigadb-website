<?php

class m200304_170422_create_gigadb_user_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createTable('gigadb_user', array(
            'id' => 'integer NOT NULL',
            'email' => 'string NOT NULL',
            'password' => 'string NOT NULL',
            'first_name' => 'string NOT NULL',
            'last_name' => 'string NOT NULL',
            'affiliation' => 'string NOT NULL',
            'role' => 'string DEFAULT \'user\'::character varying (255) NOT NULL',
            'is_activated' => 'boolean DEFAULT \'false\'::boolean NOT NULL',
            'newsletter' => 'boolean DEFAULT \'true\'::boolean NOT NULL',
            'previous_newsletter_state' => 'boolean DEFAULT \'false\'::boolean NOT NULL',
            'facebook_id' => 'text',
            'twitter_id' => 'text',
            'linkedin_id' => 'text',
            'google_id' => 'text',
            'username' => 'text NOT NULL',
            'orcid_id' => 'text',
            'preferred_link' => 'string DEFAULT \'EBI\'::character varying (255)'
        ));

        // Create sequence using plain SQL
        Yii::app()->db->createCommand('CREATE SEQUENCE gigadb_user_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;')->execute();
        Yii::app()->db->createCommand('ALTER SEQUENCE gigadb_user_id_seq OWNED BY gigadb_user.id;')->execute();
        Yii::app()->db->createCommand('ALTER TABLE ONLY gigadb_user ALTER COLUMN id SET DEFAULT nextval(\'gigadb_user_id_seq\'::regclass);')->execute();

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
