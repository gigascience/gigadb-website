<?php

class m190425_100155_add_funding_to_dataset_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"dataset\"
ADD \"funding\" smallint NULL;");
	}

	public function down()
	{
		$this->execute("ALTER TABLE \"dataset\"
DROP \"funding\";");
	}
}