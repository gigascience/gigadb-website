<?php

class m200305_183350_create_file_type_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE file_type (
                id integer NOT NULL,
                name character varying(100) NOT NULL,
                description text DEFAULT \'\'::text NOT NULL,
                edam_ontology_id character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE file_type_id_seq
                START WITH 15
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE file_type_id_seq 
                OWNED BY file_type.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY file_type 
                ALTER COLUMN id SET DEFAULT nextval(\'file_type_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY file_type
                ADD CONSTRAINT file_type_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('file_type', array(
            'id' => '132',
            'name' =>'Sequence assembly',
            'description' => 'An assembly of fragments of a (typically genomic) DNA sequence. Typically, an assembly is a collection of contigs (for example ESTs and genomic DNA fragments) that are ordered, aligned and merged. Annotation of the assembled sequence might be included.',
            'edam_ontology_id' => 'data_0925'
        ));
        $this->insert('file_type', array(
            'id' => '9',
            'name' =>'Genome sequence',
            'description' => 'nucleotide sequence file, these could be filtered or raw, but usually GigaDB only hosts processed sequence files, the raw should be deposited in the SRA',
            'edam_ontology_id' => 'data_2977'
        ));
        $this->insert('file_type', array(
            'id' => '6',
            'name' =>'Annotation',
            'description' => 'This Is a broad concept and covers all files containing annotation of something, this could be sequence features, or image features or many other things. If possible a more specific type should be used.',
            'edam_ontology_id' => 'none'
        ));
        $this->insert('file_type', array(
            'id' => '4',
            'name' =>'Coding sequence',
            'description' => 'Predcited protein-coding regions (CDS or exon) or open reading frames in nucleotide sequences.',
            'edam_ontology_id' => 'operation_0436'
        ));
        $this->insert('file_type', array(
            'id' => '127',
            'name' =>'Expression data',
            'description' => 'Files representing the analysis of levels and patterns of synthesis of gene products (proteins and/or functional RNA) including interpretation in functional terms of gene expression data.',
            'edam_ontology_id' => 'operation_0436'
        ));
        $this->insert('file_type', array(
            'id' => '5',
            'name' =>'Protein sequence',
            'description' => 'One or more protein sequences, possibly with associated annotation. Usually translations of coding region predictions.',
            'edam_ontology_id' => 'data_2976'
        ));
        $this->insert('file_type', array(
            'id' => '64',
            'name' =>'Article',
            'description' => 'A document of scientific text, for example; a full text article from a scientific journal, or a supplemental file, or even formal unpublished documentation of software/scripts'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('file_type');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE file_type_id_seq;')->execute();
    }
}
