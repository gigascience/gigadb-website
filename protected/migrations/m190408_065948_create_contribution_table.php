<?php

class m190408_065948_create_contribution_table extends CDbMigration
{
	public function up()
	{
	    $this->execute('CREATE SEQUENCE contribution_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;');

	    $this->execute('
CREATE TABLE "contribution" (
    "id" integer DEFAULT nextval(\'contribution_id_seq\') NOT NULL,
    "name" character varying(255) NOT NULL,
    CONSTRAINT "contribution_id" PRIMARY KEY ("id"),
    CONSTRAINT "contribution_name" UNIQUE ("name")
) WITH (oids = false);');
	}

	public function down()
	{
		echo "m190408_065948_create_contribution_table does not support migration down.\n";
		return false;
	}
}