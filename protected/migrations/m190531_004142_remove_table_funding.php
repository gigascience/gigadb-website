<?php

class m190531_004142_remove_table_funding extends CDbMigration
{
	public function up()
	{
        $this->execute('DROP TABLE "funding";');
	}

	public function down()
	{
		echo "m190531_004142_remove_table_funding does not support migration down.\n";
		return false;
	}
}