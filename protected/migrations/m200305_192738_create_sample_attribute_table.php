<?php

class m200305_192738_create_sample_attribute_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE sample_attribute (
                id integer NOT NULL,
                sample_id integer NOT NULL,
                attribute_id integer NOT NULL,
                value character varying(10000),
                unit_id character varying(30));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE sample_attribute_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE sample_attribute_id_seq 
                OWNED BY sample_attribute.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY sample_attribute 
                ALTER COLUMN id SET DEFAULT nextval(\'sample_attribute_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY sample_attribute
                ADD CONSTRAINT sample_attribute_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY sample_attribute
                ADD CONSTRAINT sample_attribute_fkey FOREIGN KEY (attribute_id) 
                REFERENCES attribute(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY sample_attribute
                ADD CONSTRAINT sample_attribute_sample_id_fkey FOREIGN KEY (sample_id) 
                REFERENCES sample(id) ON DELETE CASCADE;'
        );

        $sql_altertab5 = sprintf(
            'ALTER TABLE ONLY sample_attribute
                ADD CONSTRAINT sample_attribute_unit_id_fkey FOREIGN KEY (unit_id) 
                REFERENCES unit(id);'
        );

        $sql_createindex = sprintf(
            'CREATE INDEX fki_sample_attribute_fkey ON sample_attribute 
                USING btree (attribute_id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4, $sql_altertab5 ,$sql_createindex);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('sample_attribute', array(
            'id' => '14770',
            'sample_id' => '336',
            'attribute_id' =>'336',
            'value' => 'TY-2482'

        ));
        $this->insert('sample_attribute', array(
            'id' => '14775',
            'sample_id' => '336',
            'attribute_id' =>'337',
            'value' => 'stool sample from patient with hemolytic uremic syndrome (HUS)'
        ));
        $this->insert('sample_attribute', array(
            'id' => '16621',
            'sample_id' => '336',
            'attribute_id' =>'383',
            'value' => 'O104:H4'
        ));
        $this->insert('sample_attribute', array(
            'id' => '25899',
            'sample_id' => '336',
            'attribute_id' =>'327',
            'value' => 'SRS211184'
        ));

        $this->insert('sample_attribute', array(
            'id' => '5025',
            'sample_id' => '453',
            'attribute_id' =>'282',
            'value' => 'Tom Gilbert'
        ));
        $this->insert('sample_attribute', array(
            'id' => '10338',
            'sample_id' => '453',
            'attribute_id' =>'376',
            'value' => '1.5'
        ));
        $this->insert('sample_attribute', array(
            'id' => '12450',
            'sample_id' => '453',
            'attribute_id' =>'314',
            'value' => 'BGI, Copenhagen'
        ));
        $this->insert('sample_attribute', array(
            'id' => '13028',
            'sample_id' => '453',
            'attribute_id' =>'270',
            'value' => 'Denmark'
        ));
        $this->insert('sample_attribute', array(
            'id' => '13193',
            'sample_id' => '453',
            'attribute_id' =>'269',
            'value' => 'not recorded'
        ));
        $this->insert('sample_attribute', array(
            'id' => '14788',
            'sample_id' => '453',
            'attribute_id' =>'320',
            'value' => 'LC'
        ));
        $this->insert('sample_attribute', array(
            'id' => '15809',
            'sample_id' => '453',
            'attribute_id' =>'277',
            'value' => 'diploid'
        ));
        $this->insert('sample_attribute', array(
            'id' => '16197',
            'sample_id' => '453',
            'attribute_id' =>'319',
            'value' => 'Tom Gilbert'
        ));
        $this->insert('sample_attribute', array(
            'id' => '16213',
            'sample_id' => '453',
            'attribute_id' =>'318',
            'value' => 'Anders Christiansen, Danish racing pigeon association. Original sample destroyed. Obtaining grandchild'
        ));
        $this->insert('sample_attribute', array(
            'id' => '17036',
            'sample_id' => '453',
            'attribute_id' =>'200',
            'value' => 'male'
        ));
        $this->insert('sample_attribute', array(
            'id' => '17660',
            'sample_id' => '453',
            'attribute_id' =>'282',
            'value' => 'not available'
        ));
        $this->insert('sample_attribute', array(
            'id' => '18646',
            'sample_id' => '453',
            'attribute_id' =>'315',
            'value' => 'UBERON:0000178(blood)'
        ));
        $this->insert('sample_attribute', array(
            'id' => '25690',
            'sample_id' => '453',
            'attribute_id' =>'316',
            'value' => 'COLLI'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('sample_attribute');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE sample_attribute_id_seq;')->execute();
    }
}
