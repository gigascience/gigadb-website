<?php

class m190425_101402_create_funding_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("CREATE TABLE \"funding\" (
  \"id\" serial NOT NULL,
  \"dataset_id\" integer NOT NULL,
  \"funder_id\" integer NOT NULL,
  \"program_name\" character varying(100) NOT NULL,
  \"grant\" character varying(100) NOT NULL,
  \"pi_name\" character varying(100) NOT NULL
);");
	}

	public function down()
	{
		$this->execute("DROP TABLE \"funding\";");
	}
}