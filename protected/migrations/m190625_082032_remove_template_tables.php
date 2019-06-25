<?php

class m190625_082032_remove_template_tables extends CDbMigration
{
	public function up()
	{
        $this->execute("DROP TABLE \"template_attribute\";");
        $this->execute("DROP TABLE \"template_name\";");
	}

	public function down()
	{
		echo "m190625_082032_remove_template_tables does not support migration down.\n";
		return false;
	}
}