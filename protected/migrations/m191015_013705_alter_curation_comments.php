<?php

class m191015_013705_alter_curation_comments extends CDbMigration
{
	public function up()
	{
        $this->execute("ALTER TABLE curation_log ALTER COLUMN comments TYPE VARCHAR (1000)");
	}

	public function down()
	{
        $this->execute("ALTER TABLE curation_log ALTER COLUMN comments TYPE VARCHAR (200)");
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