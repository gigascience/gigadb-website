<?php

class m190401_134039_alter_table_image_column_photographer extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"image\"
ALTER \"photographer\" TYPE character varying(128),
ALTER \"photographer\" DROP DEFAULT,
ALTER \"photographer\" DROP NOT NULL;");
	}

	public function down()
	{
		echo "m190401_134039_alter_table_image_column_photographer does not support migration down.\n";
		return false;
	}
}