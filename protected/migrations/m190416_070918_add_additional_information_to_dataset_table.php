<?php

class m190416_070918_add_additional_information_to_dataset_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"dataset\"
ADD \"additional_information\" character varying(500) NULL;");
	}

	public function down()
	{
		echo "m190416_070918_add_additional_information_to_dataset_table does not support migration down.\n";
		return false;
	}
}