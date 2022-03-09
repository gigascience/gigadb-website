<?php

class m220126_062259_update_image_location_column_project_tab extends CDbMigration
{
	public function safeUp()
	{
        Yii::app()->db->createCommand("update project set image_location = replace(image_location,'http://gigadb.org/images/project','');")->execute();
        Yii::app()->db->createCommand("update project set image_location = replace(image_location,'http://hpc-bioinformatics.cineca.it/fusion/imgs','');")->execute();
        Yii::app()->db->createCommand("update project set image_location = replace(image_location,'https://bioinfotraining.bio.cam.ac.uk/images','');")->execute();
        Yii::app()->db->createCommand("update project set image_location = replace(image_location,'http://gigadb.org/images/data/cropped','');")->execute();
        Yii::app()->db->createCommand("ALTER TABLE project ALTER COLUMN image_location TYPE character varying(255);")->execute();
        Yii::app()->db->createCommand("update project set image_location = concat('https://assets.gigadb-cdn.net/live/images/projects/', lower(replace(replace(replace(name, ' ','_'),'_-_','_'),',','')),  image_location) where image_location != '' and image_location not like 'https://assets.gigadb-cdn.net/images/projects/%' and name not like 'LiGeA%' and name != 'https://www.carmen.org.uk';")->execute();
        Yii::app()->db->createCommand("update project set image_location = concat('https://assets.gigadb-cdn.net/live/images/projects/', replace(name, 'LiGeA: a comprehensive database of human gene fusion events', 'ligea'), image_location) where name like 'LiGeA%' and image_location not like 'https://assets.gigadb-cdn.net/images/projects/%';")->execute();
        Yii::app()->db->createCommand("update project set image_location = concat('https://assets.gigadb-cdn.net/live/images/projects/', replace(name, 'https://www.carmen.org.uk', 'carmen'), image_location) where name = 'https://www.carmen.org.uk' and image_location not like 'https://assets.gigadb-cdn.net/images/projects/%';")->execute();
	}

	public function safeDown()
	{

	}
}