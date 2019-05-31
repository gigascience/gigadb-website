<?php

class m190530_230104_contribution_id_to_dataset_author extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"author\"
DROP \"contribution_id\";");

        $this->execute('ALTER TABLE "dataset_author"
ADD "contribution_id" integer NULL;');
	}

	public function down()
	{
		echo "m190530_230104_contribution_id_to_dataset_author does not support migration down.\n";
		return false;
	}
}