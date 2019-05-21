<?php

class m190401_132731_alter_table_image_column_source extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"image\"
ALTER \"source\" TYPE character varying(256),
ALTER \"source\" DROP DEFAULT,
ALTER \"source\" DROP NOT NULL;");
	}

	public function down()
	{
		echo "m190401_132731_alter_table_image_column_source does not support migration down.\n";
		return false;
	}
}