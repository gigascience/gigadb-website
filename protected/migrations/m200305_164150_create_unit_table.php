<?php

class m200305_164150_create_unit_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE unit (
                id character varying(30) NOT NULL,
                name character varying(200),
                definition character varying(500));'
        );

        $sql_comment1 = sprintf(
            'COMMENT ON COLUMN unit.id IS \'the ID from the unit ontology\';'
        );

        $sql_comment2 = sprintf(
            'COMMENT ON COLUMN unit.name IS \'the name of the unit (taken from the Unit Ontology)\';'
        );

        $sql_comment3 = sprintf(
            'COMMENT ON COLUMN unit.definition IS \'the inition taken from the unit ontology\';;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY unit
                ADD CONSTRAINT unit_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array( $sql_createtab, $sql_comment1, $sql_comment2, $sql_comment3, $sql_altertab1);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('unit', array(
            'id' => 'UO:0000001',
            'name' =>'length unit',
            'definition' => 'A unit which is a standard measure of the distance between two points.'
        ));
        $this->insert('unit', array(
            'id' => 'UO:0000002',
            'name' =>'mass unit',
            'definition' => 'A unit which is a standard measure of the amount of matter/energy of a physical object.'
        ));
        $this->insert('unit', array(
            'id' => 'UO:0000003',
            'name' =>'time unit',
            'definition' => 'A unit which is a standard measure of the dimension in which events occur in sequence.'
        ));
        $this->insert('unit', array(
            'id' => 'UO:0000004',
            'name' =>'electric current unit',
            'definition' => 'A unit which is a standard measure of the flow of electric charge.'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('unit');
    }
}
