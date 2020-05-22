<?php

class m200305_190755_create_prefix_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE prefix (
                id integer DEFAULT nextval(\'link_prefix_id_seq\'::regclass) NOT NULL,
                prefix character(20) NOT NULL,
                url text NOT NULL,
                source character varying(128) DEFAULT \'\'::character varying,
                icon character varying(100));'
        );

        $sql_altertab = sprintf(
            'ALTER TABLE ONLY prefix
                ADD CONSTRAINT link_prefix_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array( $sql_createtab, $sql_altertab);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('prefix', array(
            'id' => '25',
            'prefix' =>'GitHub',
            'url' => 'https://github.com/',
        ));
        $this->insert('prefix', array(
            'id' => '33',
            'prefix' =>'Sample',
            'url' => 'http://www.ncbi.nlm.nih.gov/sra/?term=',
            'source' => 'NCBI'
        ));
        $this->insert('prefix', array(
            'id' => '34',
            'prefix' =>'Sample',
            'url' => 'https://www.ebi.ac.uk/ena/data/view/',
            'source' => 'EBI'
        ));
        $this->insert('prefix', array(
            'id' => '35',
            'prefix' =>'Sample',
            'url' => 'http://trace.ddbj.nig.ac.jp/DRASearch/sample?acc=',
            'source' => 'DDBJ'
        ));
        $this->insert('prefix', array(
            'id' => '36',
            'prefix' =>'BioProject',
            'url' => 'http://www.ncbi.nlm.nih.gov/bioproject/',
            'source' => 'NCBI'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('prefix');
    }
}
