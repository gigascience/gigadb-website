<?php

class m190611_064916_add_is_test_and_creation_date_to_dataset_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"dataset\"
ADD \"is_test\" smallint NULL,
ADD \"creation_date\" date NULL;");
	}

	public function down()
	{
		echo "m190611_064916_add_is_test_and_creation_date_to_dataset_table does not support migration down.\n";
		return false;
	}
}