<?php

class m190422_191535_add_column_regexp_to_prefix_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"prefix\"
ADD \"regexp\" character varying(128) NULL;");
	}

	public function down()
	{
		$this->execute("ALTER TABLE \"prefix\"
DROP \"regexp\";");
	}
}