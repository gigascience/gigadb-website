<?php

class m190607_112816_account_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('account', array(
            'id' => 'serial PRIMARY KEY',
            'doi_suffix' => 'integer NOT NULL',
            'ulogin' => 'character varying(100)',
            'utoken' => 'character varying(128)',
            'dlogin' => 'character varying(100)',
            'dtoken' => 'character varying(128)',
            'space_used' => 'bigint',
            'status' => 'character varying(100)',
            'created_at' => 'timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL',
            'updated_at' => 'timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL',
            'retired_at' => 'timestamp',
        ));

        $this->execute("CREATE OR REPLACE FUNCTION update_modified_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql'");

       $this->execute("CREATE TRIGGER update_account_modtime BEFORE UPDATE ON account
FOR EACH ROW EXECUTE PROCEDURE  update_modified_column()");

       $this->execute("CREATE OR REPLACE FUNCTION update_retired_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.retired_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql'");

    	$this->execute("CREATE TRIGGER update_account_retiredtime BEFORE UPDATE ON account
FOR EACH ROW
WHEN (OLD.status <> NEW.status AND NEW.status = 'retired')
EXECUTE PROCEDURE  update_retired_column()");

	}

	public function down()
	{
		echo "m190607_112816_account_table does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}