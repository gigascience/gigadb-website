<?php

class m190602_235606_additional_information_to_small_int extends CDbMigration
{
	public function up()
	{
	    $this->execute("UPDATE dataset
SET additional_information = NULL");

	    $this->execute("ALTER TABLE dataset ALTER COLUMN additional_information TYPE smallint USING (additional_information::smallint);");
	}

	public function down()
	{
		echo "m190602_235606_additional_information_to_small_int does not support migration down.\n";
		return false;
	}
}