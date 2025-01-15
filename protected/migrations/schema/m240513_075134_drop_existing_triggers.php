<?php

/**
 * Remove all the customized triggers as the finders will be refreshed every day in production
 */
class m240513_075134_drop_existing_triggers extends CDbMigration
{
    public function safeUp()
    {
        Yii::app()->db->createCommand("drop trigger if exists file_finder_trigger on file RESTRICT")->execute();
        Yii::app()->db->createCommand("drop trigger if exists sample_finder_trigger on sample RESTRICT")->execute();
        Yii::app()->db->createCommand("drop trigger if exists dataset_finder_trigger on dataset RESTRICT")->execute();
    }

    public function safeDown()
    {
        Yii::app()->db->createCommand("create trigger file_finder_trigger after insert or update or delete or truncate on file for each statement execute procedure refresh_file_finder()")->execute();
        Yii::app()->db->createCommand("create trigger sample_finder_trigger after insert or update or delete or truncate on sample for each statement execute procedure refresh_sample_finder()")->execute();
        Yii::app()->db->createCommand("create trigger dataset_finder_trigger after insert or update or delete or truncate on dataset for each statement execute procedure refresh_dataset_finder()")->execute();
    }
}
