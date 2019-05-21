<?php

class m190422_030657_add_columns_to_external_link_table extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"external_link\"
ALTER \"external_link_type_id\" TYPE integer,
ALTER \"external_link_type_id\" DROP DEFAULT,
ALTER \"external_link_type_id\" DROP NOT NULL,
ADD \"type\" integer NULL,
ADD \"description\" character varying(200) NULL;");
	}

	public function down()
	{
		$this->execute("ALTER TABLE \"external_link\"
ALTER \"external_link_type_id\" TYPE integer,
ALTER \"external_link_type_id\" DROP DEFAULT,
ALTER \"external_link_type_id\" SET NOT NULL,
DROP \"type\",
DROP \"description\";");
	}
}