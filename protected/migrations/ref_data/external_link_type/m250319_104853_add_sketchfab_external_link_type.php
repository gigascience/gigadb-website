<?php

class m250319_104853_add_sketchfab_external_link_type extends CDbMigration
{
	public function up()
	{
        $this->insert("external_link_type", array('name' => '3D Sketchfab'));
	}

	public function down()
	{
        $this->delete("external_link_type", "name='3D Sketchfab'");
//		echo "m250319_104853_add_sketchfab_externa_link_type does not support migration down.\n";
//		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}