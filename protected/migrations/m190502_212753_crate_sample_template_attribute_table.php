<?php

class m190502_212753_crate_sample_template_attribute_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("CREATE TABLE \"sample_template_attribute\" (
    \"id\" serial NOT NULL,
    \"sample_template_id\" integer,
    \"attribute_id\" integer,
    CONSTRAINT \"sample_template_attribute_pkey\" PRIMARY KEY (\"id\"),
    CONSTRAINT \"sample_template_attribute_sample_template_id_fkey\" FOREIGN KEY (sample_template_id) REFERENCES sample_template(id) ON DELETE CASCADE NOT DEFERRABLE,
    CONSTRAINT \"sample_template_attribute_attribute_id_fkey\" FOREIGN KEY (attribute_id) REFERENCES attribute(id) ON DELETE CASCADE NOT DEFERRABLE
) WITH (oids = false);");
	}

	public function down()
	{
		$this->execute("DROP TABLE \"sample_template_attribute\";");
	}
}