<?php

class m220328_033008_update_keywords_dataset_attributes_tab extends CDbMigration
{


	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	    Yii::app()->db->createCommand("update dataset_attributes set value = lower(value) where value != lower(value);")->execute();
	}

	public function safeDown()
	{
	}
}