<?php

class m181108_105104_widen_user_password_column extends CDbMigration
{
	/**
	 * the password column need to be bigger to accomdate the new algorithm for password hashes
	 *
	 */
	public function up()
	{
		$this->alterColumn("gigadb_user","password","varchar(128)");
	}

	/**
	 * because column size is trivial and relatively inconsequential if it's bigger than necessary
	 * and because new password hashes are bigger than the previous size
	 * the down() migration will keep the size unchanged to make potential reverting of migrations smoother
	 * and not throw exceptions unenecessarily
	 */
	public function down()
	{
		$this->alterColumn("gigadb_user","password","varchar(128)");
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