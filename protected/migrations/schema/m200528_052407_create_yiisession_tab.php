<?php

class m200528_052407_create_yiisession_tab extends CDbMigration
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
        echo "m200528_052407_create_yiisession_tab does not support migration down.\n";
        return false;
    }
}
