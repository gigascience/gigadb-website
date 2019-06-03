<?php

class m190602_235523_remove_type_from_external_link extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"external_link\"
DROP \"type\";");
	}

	public function down()
	{
		echo "m190602_235523_remove_type_from_external_link does not support migration down.\n";
		return false;
	}
}