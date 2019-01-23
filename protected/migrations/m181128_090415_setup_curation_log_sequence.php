<?php

class m181128_090415_setup_curation_log_sequence extends CDbMigration
{
	public function up()
	{
		$this->execute("ALTER TABLE ONLY curation_log ALTER COLUMN id SET DEFAULT nextval('curation_log_id_seq'::regclass)");
	}

	public function down()
	{
		$this->execute("ALTER TABLE ONLY curation_log ALTER COLUMN id SET DEFAULT NULL");
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