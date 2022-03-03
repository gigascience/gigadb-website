<?php

class m200529_036000_create_reset_password_request extends CDbMigration
{
    /**
     * Create table schema based on https://paragonie.com/blog/2016/09/untangling-forget-me-knot-secure-account-recovery-made-simple
     * 
     * @param selector A non-hashed random string used to fetch a request from persistence
     * @param hashed_token The hashed token (verifier) used to verify a reset request
     * @return bool|void
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS reset_password_request (
            selector character varying(128) NOT NULL PRIMARY KEY,
            hashed_token character varying(128),
            requested_at timestamp without time zone,
            expires_at timestamp without time zone,
            gigadb_user_id integer NOT NULL);");

        $this->execute("ALTER TABLE ONLY reset_password_request
            ADD CONSTRAINT reset_password_requests_gigadb_user_id_fkey FOREIGN KEY (gigadb_user_id) REFERENCES gigadb_user(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->dropTable('reset_password_request');
    }
}
