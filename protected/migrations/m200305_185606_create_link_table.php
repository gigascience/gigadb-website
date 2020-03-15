<?php

class m200305_185606_create_link_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE link (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                is_primary boolean DEFAULT false NOT NULL,
                link character varying(100) NOT NULL,
                description character varying(200));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE link_id_seq
                START WITH 66
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE link_id_seq 
                OWNED BY link.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY link 
                ALTER COLUMN id SET DEFAULT nextval(\'link_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY link
                ADD CONSTRAINT link_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY link
                ADD CONSTRAINT link_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('link', array(
            'id' => '30',
            'dataset_id' =>'15',
            'is_primary' =>'t',
            'link' =>'SRA:SRP006916'
        ));
        $this->insert('link', array(
            'id' => '31',
            'dataset_id' =>'15',
            'is_primary' =>'t',
            'link' =>'BioProject:PRJNA67657'
        ));
        $this->insert('link', array(
            'id' => '60',
            'dataset_id' =>'29',
            'is_primary' =>'t',
            'link' =>'GENBANK:AEHK00000000'
        ));
        $this->insert('link', array(
            'id' => '61',
            'dataset_id' =>'29',
            'is_primary' =>'f',
            'link' =>'SRA:SRP003590'
        ));
        $this->insert('link', array(
            'id' => '59',
            'dataset_id' =>'29',
            'is_primary' =>'t',
            'link' =>'BioProject:PRJNA51409'
        ));
        $this->insert('link', array(
            'id' => '27',
            'dataset_id' =>'13',
            'is_primary' =>'f',
            'link' =>'SRA:SRP003591'
        ));
        $this->insert('link', array(
            'id' => '26',
            'dataset_id' =>'13',
            'is_primary' =>'t',
            'link' =>'GENBANK:AEHL00000000'
        ));
        $this->insert('link', array(
            'id' => '25',
            'dataset_id' =>'13',
            'is_primary' =>'t',
            'link' =>'BioProject:PRJNA51411'
        ));
        $this->insert('link', array(
            'id' => '56',
            'dataset_id' =>'25',
            'is_primary' =>'t',
            'link' =>'SRA:SRP000962'
        ));
        $this->insert('link', array(
            'id' => '58',
            'dataset_id' =>'25',
            'is_primary' =>'t',
            'link' =>'GENBANK:ACTA00000000'
        ));
        $this->insert('link', array(
            'id' => '57',
            'dataset_id' =>'25',
            'is_primary' =>'t',
            'link' =>'BioProject:PRJNA38683'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('link');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE link_id_seq;')->execute();
    }
}
