<?php

class m200529_035151_create_user_command_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS user_command (
            id integer NOT NULL,
            action_label character varying(32) NOT NULL,
            requester_id integer NOT NULL,
            actioner_id integer,
            actionable_id integer NOT NULL,
            request_date timestamp without time zone,
            action_date timestamp without time zone,
            status character varying(32) NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS user_command_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE user_command_id_seq OWNED BY user_command.id;");

        $this->execute("ALTER TABLE ONLY user_command 
            ALTER COLUMN id SET DEFAULT nextval('user_command_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY user_command
            ADD CONSTRAINT user_command_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE user_command_id_seq CASCADE;");
        $this->dropTable('user_command');
    }
}
