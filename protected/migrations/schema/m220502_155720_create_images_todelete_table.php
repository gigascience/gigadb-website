<?php

class m220502_155720_create_images_todelete_table extends CDbMigration
{
	public function safeUp()
	{
        $this->execute("CREATE TABLE IF NOT EXISTS images_todelete (
            id SERIAL PRIMARY KEY,
            url text NOT NULL,
            created_at timestamp without time zone DEFAULT now()
            )");
	}

	public function safeDown()
	{
		$this->dropTable("images_todelete");
	}


}