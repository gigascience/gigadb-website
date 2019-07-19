<?php

class m190719_094755_update_regexp_for_bio_project extends CDbMigration
{
	public function up()
	{
        $this->execute("UPDATE \"prefix\" SET \"regexp\" = '/^PRJ[DEN][A-Z]\d+$/' WHERE \"prefix\" = 'BioProject';");
	}

	public function down()
	{
		echo "m190719_094755_update_regexp_for_bio_project does not support migration down.\n";
		return false;
	}
}