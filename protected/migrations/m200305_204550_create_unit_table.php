<?php

class m200305_204550_create_unit_table extends CDbMigration
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

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE species_id_seq 
                OWNED BY species.id;'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('publisher', array(
            'id' => '1',
            'name' =>'GigaScience'
        ));
        $this->insert('publisher', array(
            'id' => '2',
            'name' =>'BGI Shenzhen'
        ));
        $this->insert('publisher', array(
            'id' => '3',
            'name' =>'GigaScience Database'
        ));
        $this->insert('publisher', array(
            'id' => '4',
            'name' =>'UC Davis'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('publisher');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE publisher_id_seq;')->execute();
    }
}
