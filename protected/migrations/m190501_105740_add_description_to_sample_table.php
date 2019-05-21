<?php

class m190501_105740_add_description_to_sample_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"sample\"
ADD \"description\" character varying(100) NULL;");
	}

	public function down()
	{
		$this->execute("ALTER TABLE \"sample\"
DROP \"description\";");
	}
}