<?php

class m190530_225606_add_test_data_to_contribution_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("INSERT INTO \"contribution\" (\"id\", \"name\", \"source\", \"description\") VALUES
(1,	'Contribution1',	'Source1',	'Description1'),
(2,	'Contribution2',	'Source2',	'Description2'),
(3,	'Contribution3',	'Source3',	'Description3');");
	}

	public function down()
	{
		echo "m190530_225606_add_test_data_to_contribution_table does not support migration down.\n";
		return false;
	}
}