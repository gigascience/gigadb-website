<?php

class m190603_094018_update_template_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"sample_template\"
ALTER \"name\" TYPE character varying(50),
ALTER \"name\" DROP DEFAULT,
ALTER \"name\" SET NOT NULL,
ADD \"template_description\" character varying(255) NULL,
ADD \"notes\" character varying(255) NULL;");

        $this->execute("ALTER TABLE \"sample_template\" RENAME \"name\" TO \"template_name\";");

        $this->execute("ALTER TABLE \"sample_template\" RENAME TO \"template_name\";");
	}

	public function down()
	{
		echo "m190603_094018_update_template_table does not support migration down.\n";
		return false;
	}
}