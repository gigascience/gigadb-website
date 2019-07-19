<?php

class m190717_112410_update_external_link_type extends CDbMigration
{
	public function up()
	{
	    try {
            $this->execute("INSERT INTO \"external_link_type\" (\"id\", \"name\") VALUES (3, 'Additional information');");
        } catch (Exception $e) {
            $this->execute("UPDATE \"external_link_type\" SET \"name\" = 'Additional information' WHERE \"id\" = 3;");
        }

        try {
            $this->execute("INSERT INTO \"external_link_type\" (\"id\", \"name\") VALUES (4, 'Genome browser');");
        } catch (Exception $e) {
            $this->execute("UPDATE \"external_link_type\" SET \"name\" = 'Genome browser' WHERE \"id\" = 4;");
        }

        try {
            $this->execute("INSERT INTO \"external_link_type\" (\"id\", \"name\") VALUES (5, 'Protocols.io');");
        } catch (Exception $e) {
            $this->execute("UPDATE \"external_link_type\" SET \"name\" = 'Protocols.io' WHERE \"id\" = 5;");
        }

        try {
            $this->execute("INSERT INTO \"external_link_type\" (\"id\", \"name\") VALUES (6, 'JBrowse');");
        } catch (Exception $e) {
            $this->execute("UPDATE \"external_link_type\" SET \"name\" = 'JBrowse' WHERE \"id\" = 6;");
        }

        try {
            $this->execute("INSERT INTO \"external_link_type\" (\"id\", \"name\") VALUES (7, '3D Models');");
        } catch (Exception $e) {
            $this->execute("UPDATE \"external_link_type\" SET \"name\" = '3D Models' WHERE \"id\" = 7;");
        }
	}

	public function down()
	{
		echo "m190717_112410_update_external_link_type does not support migration down.\n";
		return false;
	}
}