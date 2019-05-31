<?php

class m190530_224541_images_source_and_photographer_can_be_null extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"image\"
ALTER \"photographer\" TYPE character varying(128),
ALTER \"photographer\" DROP DEFAULT,
ALTER \"photographer\" SET NOT NULL,
ALTER \"source\" TYPE character varying(256),
ALTER \"source\" DROP DEFAULT,
ALTER \"source\" SET NOT NULL;");
	}

	public function down()
	{
        return false;
	}
}