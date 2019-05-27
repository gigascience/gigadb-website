<?php

class m190527_025615_dataset_upload_status extends CDbMigration
{
	public function up()
	{
        $this->execute("ALTER TABLE dataset ALTER COLUMN upload_status SET DEFAULT 'AuthorReview'");
	}

	public function down()
	{
        $this->execute("ALTER TABLE dataset ALTER COLUMN upload_status SET DEFAULT 'Pending'");
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