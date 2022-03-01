<?php

class m220218_062834_update_url_image_tab extends CDbMigration
{
	public function safeUp()
	{
	    Yii::app()->db->createCommand("update image set url = replace(url, 'http://gigadb.org/','https://assets.gigadb-cdn.net/images/datasets/') where url like 'http://gigadb.org/%';")->execute();
        // The empty value would be converted to null after set up the database from csv files, so the following step is needed to convert it back to empty again.
	    Yii::app()->db->createCommand("update image set url = '' where url is null;")->execute();
	    Yii::app()->db->createCommand("update image set url = 'https://assets.gigadb-cdn.net/images/datasets/no_image.png' where url like '' and location like 'no_image%';")->execute();
	}

	public function safeDown()
	{
	}
}