<?php

/**
 * Class m220128_162431_search_materialized_views_trigger
 *
 * migrations to install trigger for refreshing search materialized views
 */
class m220128_162431_search_materialized_views_trigger extends CDbMigration
{



	public function safeUp()
	{

	    //create trigger for file_finder
        Yii::app()->db->createCommand("create or replace function refresh_file_finder()
returns trigger language plpgsql
as $$
begin
    refresh materialized view file_finder;
    return null;
end $$")->execute();

        Yii::app()->db->createCommand("drop trigger if exists file_finder_trigger on file RESTRICT")->execute();
        Yii::app()->db->createCommand("create trigger file_finder_trigger
after insert or update or delete or truncate
on file for each statement 
execute procedure refresh_file_finder()")->execute();

        //create trigger for sample_finder
        Yii::app()->db->createCommand("create or replace function refresh_sample_finder()
returns trigger language plpgsql
as $$
begin
    refresh materialized view sample_finder;
    return null;
end $$")->execute();

        Yii::app()->db->createCommand("drop trigger if exists sample_finder_trigger on sample RESTRICT")->execute();
        Yii::app()->db->createCommand("create trigger sample_finder_trigger
after insert or update or delete or truncate
on sample for each statement 
execute procedure refresh_sample_finder()")->execute();

        //create trigger for dataset_finder
        Yii::app()->db->createCommand("create or replace function refresh_dataset_finder()
returns trigger language plpgsql
as $$
begin
    refresh materialized view dataset_finder;
    return null;
end $$")->execute();

        Yii::app()->db->createCommand("drop trigger if exists dataset_finder_trigger on dataset RESTRICT")->execute();
        Yii::app()->db->createCommand("create trigger dataset_finder_trigger
after insert or update or delete or truncate
on dataset for each statement 
execute procedure refresh_dataset_finder()")->execute();

    }


	public function safeDown()
	{
        Yii::app()->db->createCommand("drop trigger if exists file_finder_trigger on file RESTRICT")->execute();
        Yii::app()->db->createCommand("drop function if exists refresh_file_finder RESTRICT")->execute();

        Yii::app()->db->createCommand("drop trigger if exists sample_finder_trigger on file RESTRICT")->execute();
        Yii::app()->db->createCommand("drop function if exists refresh_sample_finder RESTRICT")->execute();

        Yii::app()->db->createCommand("drop trigger if exists dataset_finder_trigger on file RESTRICT")->execute();
        Yii::app()->db->createCommand("drop function if exists refresh_dataset_finder RESTRICT")->execute();
	}

}