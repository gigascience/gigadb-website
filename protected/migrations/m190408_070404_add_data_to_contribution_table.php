<?php

class m190408_070404_add_data_to_contribution_table extends CDbMigration
{
	public function up()
	{
	    $this->execute('INSERT INTO "contribution" ("id", "name") VALUES
(1,	\'Contribution1\'),
(2,	\'Contribution2\'),
(3,	\'Contribution3\');');
	}

	public function down()
	{
		echo "m190408_070404_add_data_to_contribution_table does not support migration down.\n";
		return false;
	}
}