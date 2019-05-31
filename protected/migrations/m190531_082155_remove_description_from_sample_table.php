<?php

class m190531_082155_remove_description_from_sample_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"sample\"
DROP \"description\";");
	}

	public function down()
	{
		echo "m190531_082155_remove_description_from_sample_table does not support migration down.\n";
		return false;
	}
}