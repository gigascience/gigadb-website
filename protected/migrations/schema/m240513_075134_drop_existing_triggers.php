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
        Yii::app()->db->createCommand("drop trigger if exists dataset_finder_trigger on file RESTRICT")->execute();
    }

    public function safeDown()
    {
        echo "m240513_075134_drop_existing_triggers does not support migration down.\n";
        return false;
    }
}
