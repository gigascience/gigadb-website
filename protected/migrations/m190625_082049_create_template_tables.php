<?php

class m190625_082049_create_template_tables extends CDbMigration
{
	public function up()
	{
        $this->execute('CREATE SEQUENCE template_name_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;');

        $this->execute('
CREATE TABLE "template_name" (
    "id" integer DEFAULT nextval(\'template_name_id_seq\') NOT NULL,
    "template_name" character varying(50) NOT NULL,
    "template_description" character varying(255) NULL,
    "notes" character varying(255) NULL,
    CONSTRAINT "template_name_id" PRIMARY KEY ("id")
) WITH (oids = false);');

        $this->execute('CREATE SEQUENCE template_attribute_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;');

        $this->execute('
CREATE TABLE "template_attribute" (
    "id" integer DEFAULT nextval(\'template_attribute_id_seq\') NOT NULL,
    "template_name_id" integer NULL,
    "attribute_id" integer NULL,
    CONSTRAINT "template_attribute_id" PRIMARY KEY ("id"),
    CONSTRAINT "template_attribute_template_name_id_fkey" FOREIGN KEY (template_name_id) REFERENCES template_name(id) ON DELETE CASCADE NOT DEFERRABLE,
    CONSTRAINT "template_attribute_attribute_id_fkey" FOREIGN KEY (attribute_id) REFERENCES attribute(id) ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);');
	}

	public function down()
	{
        $this->execute("DROP TABLE \"template_attribute\";");
        $this->execute("DROP TABLE \"template_name\";");
	}
}