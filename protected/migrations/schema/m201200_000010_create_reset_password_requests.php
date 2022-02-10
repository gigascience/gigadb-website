<?php

class m201200_000010_create_reset_password_requests extends CDbMigration
{
    public function safeUp()
    {
        // selector    A non-hashed random string used to fetch a request from persistence
        // hashedToken The hashed token used to verify a reset request
        $this->execute("CREATE TABLE IF NOT EXISTS reset_password_requests (
            selector varchar(128),
            hashed_token varchar(128),
            requested_at timestamp without time zone,
            expires_at timestamp without time zone,
            gigadb_user_id integer NOT NULL;");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS reset_password_requests_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER TABLE ONLY reset_password_requests
            ADD CONSTRAINT reset_password_requests_pkey PRIMARY KEY (selector);");

        $this->execute("ALTER TABLE ONLY reset_password_requests
            ADD CONSTRAINT reset_password_requests_gigadb_user_id_fkey FOREIGN KEY (gigadb_user_id) REFERENCES gigadb_user(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE reset_password_requests_id_seq CASCADE;");
        $this->dropTable('reset_password_requests');
    }
}
