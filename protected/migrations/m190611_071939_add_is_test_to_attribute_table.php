<?php

class m190611_071939_add_is_test_to_attribute_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"attribute\"
ADD \"is_test\" smallint NULL;");
	}

	public function down()
	{
		echo "m190611_071939_add_is_test_to_attribute_table does not support migration down.\n";
		return false;
	}
}