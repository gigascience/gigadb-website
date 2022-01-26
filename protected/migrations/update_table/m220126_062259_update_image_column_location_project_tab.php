<?php

class m220126_062259_update_image_column_location_project_tab extends CDbMigration
{
	public function safeUp()
	{
        $this->execute("update project set image_location = replace(image_location,'http://gigadb.org','');");
	}

	public function safeDown()
	{
        $this->execute("update project set image_location = concat('http://gigadb.org', image_location);");
	}
}