<?php

class m200305_171500_create_sample_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE sample (
                id integer NOT NULL,
                species_id integer NOT NULL,
                name character varying(100) DEFAULT \'SAMPLE:SRS188811\'::character varying NOT NULL,
                consent_document character varying(45),
                submitted_id integer,
                submission_date date,
                contact_author_name character varying(45),
                contact_author_email character varying(100),
                sampling_protocol character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE sample_id_seq
                START WITH 210
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE sample_id_seq 
                OWNED BY sample.id;'
        );

        $sql_createview = sprintf(
            'CREATE VIEW sample_number AS
                SELECT count(sample.id) AS count 
                FROM sample;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY sample 
                ALTER COLUMN id SET DEFAULT nextval(\'sample_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY sample
                ADD CONSTRAINT sample_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY sample
                ADD CONSTRAINT sample_species_id_fkey FOREIGN KEY (species_id) 
                REFERENCES species(id) ON DELETE CASCADE;'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY sample
                ADD CONSTRAINT sample_submitted_id_fkey FOREIGN KEY (submitted_id) 
                REFERENCES gigadb_user(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_createview, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('sample', array(
            'id' => '336',
            'species_id' => '14',
            'name' => 'TY-2482',
            'submitted_id' => '3',
            'submission_date' => '2011-06-03',
            'contact_author_name' => 'Junjie Qin',
            'contact_author_email' => 'qinjj@genomics.org.cn'
        ));
        $this->insert('sample', array(
            'id' => '457',
            'species_id' => '28',
            'name' => 'A. suum',
            'submitted_id' => '17',
            'submission_date' => '2011-11-12',
            'contact_author_name' => 'Bo Li',
            'contact_author_email' => 'libo@genomics.cn'
        ));
        $this->insert('sample', array(
            'id' => '334',
            'species_id' => '12',
            'name' => 'CE',
            'submitted_id' => '8',
            'submission_date' => '2011-07-06',
            'contact_author_name' => 'Guojie Zhang',
            'contact_author_email' => 'zhanggj@genomics.cn'
        ));
        $this->insert('sample', array(
            'id' => '453',
            'species_id' => '27',
            'name' => 'CR',
            'submitted_id' => '8',
            'submission_date' => '2011-07-06',
            'contact_author_name' => 'Guojie Zhang',
            'contact_author_email' => 'zhanggj@genomics.cn'
        ));
        $this->insert('sample', array(
            'id' => '456',
            'species_id' => '24',
            'name' => 'Danish Tumbler',
            'submitted_id' => '8',
            'submission_date' => '2011-07-06',
            'contact_author_name' => 'Guojie Zhang',
            'contact_author_email' => 'zhanggj@genomics.cn'
        ));
        $this->insert('sample', array(
            'id' => '452',
            'species_id' => '23',
            'name' => 'SAMPLE:SRS004381',
            'submitted_id' => '8',
            'submission_date' => '2011-07-06',
            'contact_author_name' => 'Guojie Zhang',
            'contact_author_email' => 'zhanggj@genomics.cn'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE sample_id_seq CASCADE;')->execute();
        // Need to use CASCADE to drop view sample_number which is dependent on
        // Sample table
        Yii::app()->db->createCommand("DROP TABLE sample CASCADE")->execute();
    }
}
