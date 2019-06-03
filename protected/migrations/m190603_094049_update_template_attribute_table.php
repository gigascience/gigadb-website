<?php

class m190603_094049_update_template_attribute_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"sample_template_attribute\" RENAME \"sample_template_id\" TO \"template_name_id\";");

        $this->execute("ALTER TABLE \"sample_template_attribute\" RENAME TO \"template_attribute\";");
	}

	public function down()
	{
		echo "m190603_094049_update_template_attribute_table does not support migration down.\n";
		return false;
	}
}