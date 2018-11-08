<?php

class m181108_105104_widen_user_password_column extends CDbMigration
{
	public function up()
	{
		$this->alterColumn("gigadb_user","password","varchar(128)");
	}

	public function down()
	{
		echo "m181108_105104_widen_user_password_column does not support migration down.\n";
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