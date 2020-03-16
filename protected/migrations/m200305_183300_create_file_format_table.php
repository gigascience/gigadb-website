<?php

class m200305_183300_create_file_format_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE file_format (
                id integer NOT NULL,
                name character varying(20) NOT NULL,
                description text DEFAULT \'\'::text NOT NULL,
                edam_ontology_id character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE file_format_id_seq
                START WITH 26
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE file_format_id_seq 
                OWNED BY file_format.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY file_format 
                ALTER COLUMN id SET DEFAULT nextval(\'file_format_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY file_format
                ADD CONSTRAINT file_format_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('file_format', array(
            'id' => '2',
            'name' =>'FASTA',
            'description' => '(.fa, .fasta, .chr, .seq, .cds, .pep, .scaffold, .scafseq, .fna, .ffn, .faa, .frn) A text-based format which begins with a single-line description, followed by lines of sequence data',
            'edam_ontology_id' => 'format_1929'
            ));
        $this->insert('file_format', array(
            'id' => '7',
            'name' =>'FASTQ',
            'description' => '(.fq, .fastq) - the FASTQ format stores sequences (usually nucleotide sequence) and Phred qualities in a single file.',
            'edam_ontology_id' => 'format_1930'
        ));
        $this->insert('file_format', array(
            'id' => '12',
            'name' =>'KEGG',
            'description' => '(.kegg) - the Web Gene Ontology (WEGO) Annotation format consists of the protein ID, followed by column(s) that are the KEGG (Kyoto Encyclopedia of Genes and Genomes) ID(s):\r+'
        ));
        $this->insert('file_format', array(
            'id' => '11',
            'name' =>'IPR',
            'description' => '(.ipr, .iprscan) - the Web Gene Ontology (WEGO) Annotation format consists of the protein ID, followed by column(s) that are the IPR (InterPro) ID(s):\r+',
            'edam_ontology_id' => 'format_1341'
        ));
        $this->insert('file_format', array(
            'id' => '5',
            'name' =>'GFF',
            'description' => '(.gff) - The General Feature Format (GFF) is used for describing genes and other features of DNA, RNA and protein sequences',
            'edam_ontology_id' => 'format_2305'
        ));
        $this->insert('file_format', array(
            'id' => '43',
            'name' =>'TSV',
            'description' => '(.tsv) Tabular data represented as tab-separated values in a text file.',
            'edam_ontology_id' => 'format_3475'
        ));
        $this->insert('file_format', array(
            'id' => '64',
            'name' =>'MP4',
            'description' => '(.mp4) MPEG-4 Part 14 or MP4 is a digital multimedia container format most commonly used to store video and audio,',
            'edam_ontology_id' => 'format_3475'
        ));
        $this->insert('file_format', array(
            'id' => '6',
            'name' =>'UNKNOWN',
            'description' => 'any other file format not defined here'
        ));
        $this->insert('file_format', array(
            'id' => '54',
            'name' =>'DOC',
            'description' => '(.doc) microsoft word format',
            'edam_ontology_id' => 'format_3507'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE file_format_id_seq CASCADE;')->execute();
        $this->dropTable('file_format');
    }
}
