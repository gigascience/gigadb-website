<?php

class m190624_171829_add_is_deleted_to_dataset_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"dataset\"
ADD \"is_deleted\" smallint NULL;");
	}

	public function down()
	{
		$this->execute("ALTER TABLE \"dataset\"
DROP \"is_deleted\";");
	}
}