<?php

class m200305_171450_create_species_table extends CDbMigration
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
            'CREATE SEQUENCE species_id_seq
                START WITH 500
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE species_id_seq 
                OWNED BY species.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY species 
                ALTER COLUMN id SET DEFAULT nextval(\'species_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY species
                ADD CONSTRAINT species_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('species', array(
            'id' => '12',
            'tax_id' => '9541',
            'common_name' => 'Crab-eating macaque',
            'genbank_name' => 'crab-eating macaque',
            'scientific_name' => 'Macaca fascicularis'
        ));
        $this->insert('species', array(
            'id' => '14',
            'tax_id' => '563',
            'common_name' => 'E. coli',
            'scientific_name' => 'Escherichia coli'
        ));
        $this->insert('species', array(
            'id' => '23',
            'tax_id' => '9646',
            'common_name' => 'Giant panda',
            'genbank_name' => 'giant panda',
            'scientific_name' => 'Ailuropoda melanoleuca'
        ));
        $this->insert('species', array(
            'id' => '24',
            'tax_id' => '8932',
            'common_name' => 'Domestic pigeon',
            'genbank_name' => 'Rock pigeon',
            'scientific_name' => 'Columba livia'
        ));
        $this->insert('species', array(
            'id' => '27',
            'tax_id' => '9544',
            'common_name' => 'Rhesus macaque',
            'genbank_name' => 'Rhesus monkey',
            'scientific_name' => 'Macaca mulatta'
        ));
        $this->insert('species', array(
            'id' => '28',
            'tax_id' => '6253',
            'common_name' => 'Pig roundworm',
            'genbank_name' => 'pig roundworm',
            'scientific_name' => 'Ascaris suum'
        ));
        $this->insert('species', array(
            'id' => '334',
            'tax_id' => '283',
            'scientific_name' => 'Comamonas'
        ));
        $this->insert('species', array(
            'id' => '453',
            'tax_id' => '449',
            'scientific_name' => 'Legionella hackeliae'
        ));
        $this->insert('species', array(
            'id' => '457',
            'tax_id' => '453',
            'scientific_name' => 'Legionella feeleii'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE species_id_seq CASCADE;')->execute();
        $this->dropTable('species');
    }
}
