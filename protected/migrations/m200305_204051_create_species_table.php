<?php

class m200305_204051_create_species_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE species (
                id integer NOT NULL,
                tax_id integer NOT NULL,
                common_name character varying(128),
                genbank_name character varying(128),
                scientific_name character varying(128) NOT NULL,
                eol_link character varying(100));'
        );

        $sql_createseq = sprintf(
            'CCREATE SEQUENCE species_id_seq
                START WITH 28
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE species_id_seq 
                OWNED BY species.id;'
        );

        $sql_altertab = sprintf(
            'ALTER TABLE ONLY species 
                ALTER COLUMN id SET DEFAULT nextval(\'species_id_seq\'::regclass);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab);
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
