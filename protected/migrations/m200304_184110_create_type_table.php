<?php

class m200304_184110_create_type_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE type (
                id integer NOT NULL,
                name character varying(32) NOT NULL,
                description text DEFAULT \'\'::text NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE type_id_seq 
                START WITH 6 
                INCREMENT BY 1 
                NO MINVALUE 
                NO MAXVALUE 
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE type_id_seq 
                OWNED BY type.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY type 
                ALTER COLUMN id SET DEFAULT nextval(\'type_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY type
                ADD CONSTRAINT type_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table
        $this->insert('type', array(
            'id' => '1',
            'name' =>'Epigenomic',
            'description' =>'methylation and histone modification data'
        ));
        $this->insert('type', array(
            'id' => '2',
            'name' =>'Genomic',
            'description' =>'genetic and genomic data e.g. sequence and assemblies'
        ));
        $this->insert('type', array(
            'id' => '3',
            'name' =>'Metagenomic',
            'description' =>'genetic and genomic data from environmental samples'
        ));
        $this->insert('type', array(
            'id' => '4',
            'name' =>'Transcriptomic',
            'description' =>'data relating to mRNA'
        ));
        $this->insert('type', array(
            'id' => '5',
            'name' =>'Workflow',
            'description' =>'data analysis pipelines'
        ));
        $this->insert('type', array(
            'id' => '6',
            'name' =>'Software',
            'description' =>'computational tools for analysing and managing biological data'
        ));
        $this->insert('type', array(
            'id' => '7',
            'name' =>'Imaging',
            'description' =>'data involving the visual depiction of biological samples'
        ));
        $this->insert('type', array(
            'id' => '8',
            'name' =>'Metabolomic'
        ));
        $this->insert('type', array(
            'id' => '10',
            'name' => 'Proteomic',
            'description' =>'large scale protein analysis dataset'
        ));
        $this->insert('type', array(
            'id' => '11',
            'name' => 'Neuroscience',
            'description' =>'Data from neuroscience studies.'
        ));
        $this->insert('type', array(
            'id' => '12',
            'name' => 'Network-Analysis',
            'description' =>'Data relating to network-analysis studies, either the results of network analysis or the methods.'
        ));
        $this->insert('type', array(
            'id' => '13',
            'name' => 'Genome-Mapping'
        ));
        $this->insert('type', array(
            'id' => '14',
            'name' => 'Virtual-Machine'
        ));
        $this->insert('type', array(
            'id' => '15',
            'name' => 'ElectroEncephaloGraphy(EEG)'
        ));
        $this->insert('type', array(
            'id' => '16',
            'name' => 'Metadata'
        ));
        $this->insert('type', array(
            'id' => '17',
            'name' => 'Metabarcoding'
        ));
        $this->insert('type', array(
            'id' => '18',
            'name' => 'Climate'
        ));
        $this->insert('type', array(
            'id' => '19',
            'name' => 'Ecology'
        ));
        $this->insert('type', array(
            'id' => '20',
            'name' => 'Lipidomic'
        ));
        $this->insert('type', array(
            'id' => '21',
            'name' => 'Phenotyping'
        ));
        $this->insert('type', array(
            'id' => '22',
            'name' => 'Electrophysiology',
            'description' => 'Data relating to the flow of ions (ion current) in biological tissues and, in particular, to the electrical recording techniques that enable the measurement of this flow.'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('type');
        Yii::app()->db->createCommand('DROP SEQUENCE type_id_seq;')->execute();
    }
}
