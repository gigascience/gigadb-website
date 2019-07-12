<?php

class m190712_063218_change_field_type_manuscript_id_at_dataset_table_to_char extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"dataset\"
ALTER \"manuscript_id\" TYPE character varying(50);");
	}

	public function down()
	{
		$this->execute("ALTER TABLE \"dataset\"
ALTER \"manuscript_id\" TYPE integer;");
	}
}