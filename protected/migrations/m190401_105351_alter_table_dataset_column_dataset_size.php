<?php

class m190401_105351_alter_table_dataset_column_dataset_size extends CDbMigration
{
	public function up()
	{
        $this->execute("ALTER TABLE \"dataset\"
ALTER \"dataset_size\" TYPE bigint,
ALTER \"dataset_size\" DROP DEFAULT,
ALTER \"dataset_size\" DROP NOT NULL;");
	}

	public function down()
	{
		echo "m190401_105351_alter_table_dataset_column_dataset_size does not support migration down.\n";
		return false;
	}
}