<?php

class m191224_025216_widen_curation_log_comments_column extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('curation_log','comments','varchar(2000)');
	}

	public function down()
	{
		$this->alterColumn('curation_log','comments','varchar(1000)');
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