<?php

class m190606_170904_file_status extends CDbMigration
{
	public function up()
	{
		$this->execute("ALTER TABLE file ADD COLUMN status character varying(100)");
	}

	public function down()
	{
		$this->execute("ALTER TABLE file DROP COLUMN IF EXISTS status");
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