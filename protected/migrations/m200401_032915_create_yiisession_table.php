<?php

class m200401_032915_create_yiisession_table extends CDbMigration
{
	public function up()
	{
        Yii::app()->db->createCommand()->createTable('YiiSession', array(
            'id'=>'CHAR(32) PRIMARY KEY',
            'expire'=> 'integer',
            'data'=> 'BYTEA'
        ));
    }

	public function down()
	{
	    // YiiSession table is essential
		echo "m200401_032915_create_yiisession_table does not support migration down.\n";
		return false;
	}
}
