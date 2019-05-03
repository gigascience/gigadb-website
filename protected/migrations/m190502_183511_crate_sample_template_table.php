<?php

class m190502_183511_crate_sample_template_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("CREATE TABLE \"sample_template\" (
  \"id\" serial NOT NULL,
  \"name\" character varying(255) NOT NULL
);");

	    $this->execute("ALTER TABLE \"sample_template\"
ADD CONSTRAINT \"sample_template_id\" PRIMARY KEY (\"id\");");
	}

	public function down()
	{
		$this->execute("DROP TABLE \"sample_template\";");
	}
}