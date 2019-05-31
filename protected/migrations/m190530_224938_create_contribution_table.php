<?php

class m190530_224938_create_contribution_table extends CDbMigration
{
	public function up()
	{
        $this->execute('DROP TABLE "contribution";');

        $this->execute("CREATE TABLE \"contribution\" (
   \"id\" integer DEFAULT nextval('contribution_id_seq') NOT NULL,
  \"name\" character varying(255) NOT NULL,
  \"source\" character varying(255) NOT NULL,
  \"description\" character varying(255) NOT NULL,
  CONSTRAINT \"contribution_id\" PRIMARY KEY (\"id\"),
    CONSTRAINT \"contribution_name\" UNIQUE (\"name\")
) WITH (oids = false);");

	}

	public function down()
	{
		echo "m190530_224938_create_contribution_table does not support migration down.\n";
		return false;
	}
}