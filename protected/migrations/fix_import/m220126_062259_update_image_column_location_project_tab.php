<?php

class m220126_062259_update_image_column_location_project_tab extends CDbMigration
{
	public function safeUp()
	{
        Yii::app()->db->createCommand("update project set name = lower(name);")->execute();
        Yii::app()->db->createCommand("update project set name = replace( name, ' ', '_' );")->execute();
        Yii::app()->db->createCommand("update project set name = 'carmen' where name = 'https://www.carmen.org.uk';")->execute();
        Yii::app()->db->createCommand("update project set image_location = replace(image_location,'http://gigadb.org/images/project','');")->execute();
        Yii::app()->db->createCommand("ALTER TABLE project ALTER COLUMN image_location TYPE text;")->execute();
        Yii::app()->db->createCommand("update project set image_location = concat('https://assets.gigadb-cdn.net/images/projects/', name, image_location);")->execute();
	}

	public function safeDown()
	{

	}
}