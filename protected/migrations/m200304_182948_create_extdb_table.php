<?php

class m200304_182948_create_extdb_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE extdb (
                id integer NOT NULL,
                database_name character varying(100),
                definition character varying(1000),
                database_homepage character varying(100),
                database_search_url character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE extdb_id_seq
                START WITH 10
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE extdb_id_seq
                OWNED BY extdb.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY extdb 
                ALTER COLUMN id SET DEFAULT nextval(\'extdb_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY extdb
                ADD CONSTRAINT extdb_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('extdb', array(
            'id' => '1',
            'database_name' =>'EBI BioSamples database',
            'definition' => 'The BioSamples database aggregates sample information for reference samples e.g. Coriell Cell lines and samples for which data exist in one of the EBI\'s assay databases such as ArrayExpress, the European Nucleotide archive, or Pride. It provides links to assays for specific samples, and accepts direct submissions of samples.',
            'database_homepage' => 'http://www.ebi.ac.uk/biosamples/index.html',
            'database_search_url' => 'http://www.ncbi.nlm.nih.gov/biosample/?term='
            ));
        $this->insert('extdb', array(
            'id' => '2',
            'database_name' =>'NCBI BioSamples database',
            'definition' => 'The BioSamples database aggregates sample information for reference samples e.g. Coriell Cell lines and samples for which data exist in one of the NCBI\'s assay databases such as GenBank, GEO, SRA or DB-Gap. It provides links to assays for specific samples, and accepts direct submissions of samples.',
            'database_homepage' => 'http://www.ncbi.nlm.nih.gov/biosample',
            'database_search_url' => 'http://www.ncbi.nlm.nih.gov/biosample/?term='
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE extdb_id_seq CASCADE;')->execute();
        $this->dropTable('extdb');
    }
}
