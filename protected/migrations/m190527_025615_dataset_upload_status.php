<?php

class m190527_025615_dataset_upload_status extends CDbMigration
{
	public function up()
	{
        $this->execute("ALTER TABLE dataset ALTER COLUMN upload_status SET DEFAULT 'AuthorReview'");
	}

	public function down()
	{
		echo "m190527_025615_dataset_upload_status does not support migration down.\n";
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