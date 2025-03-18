<?php

class m500000_000200_add_sketchfab_external_link_type extends CDbMigration
{
//	public function up()
//	{
//	}
//
//	public function down()
//	{
//	}


	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        $this->insert("external_link_type", array('name' => 'Sketchfab'));
        $this->execute("update external_link set external_link_type_id = (select id from external_link_type where name='Sketchfab') where url like 'https://sketchfab%'");
	}

	public function safeDown()
	{
        $this->update("external_link", array('external_link_type_id' => 5), array('like','url', 'https://sketchfab%'));
        $this->delete("external_link_type", "name='Sketchfab'");
	}

}