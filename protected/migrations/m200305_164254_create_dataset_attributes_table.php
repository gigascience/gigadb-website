<?php

class m200305_164254_create_dataset_attributes_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset_attributes (
                id integer NOT NULL,
                dataset_id integer,
                attribute_id integer,
                value character varying(200),
                units_id character varying(30),
                image_id integer,
                until_date date);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_attributes_id_seq
                START WITH 2500
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_attributes_id_seq 
                OWNED BY dataset_attributes.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY dataset_attributes 
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_attributes_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY dataset_attributes
                ADD CONSTRAINT dataset_attributes_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY dataset_attributes
                ADD CONSTRAINT dataset_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) 
                REFERENCES attribute(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY dataset_attributes
                ADD CONSTRAINT dataset_attributes_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id);'
        );

        $sql_altertab5 = sprintf(
            'ALTER TABLE ONLY dataset_attributes
                ADD CONSTRAINT dataset_attributes_units_id_fkey FOREIGN KEY (units_id) 
                REFERENCES unit(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4, $sql_altertab5);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('dataset_attributes', array(
            'id' => '13',
            'dataset_id' =>'200',
            'attribute_id' => '455',
            'value' => 'Sequence Read Archive'
        ));
        $this->insert('dataset_attributes', array(
            'id' => '14',
            'dataset_id' =>'200',
            'attribute_id' => '455',
            'value' => 'metadata'
        ));
        $this->insert('dataset_attributes', array(
            'id' => '15',
            'dataset_id' =>'200',
            'attribute_id' => '455',
            'value' => 'SQL'
        ));
        $this->insert('dataset_attributes', array(
            'id' => '16',
            'dataset_id' =>'200',
            'attribute_id' => '455',
            'value' => 'experimental protocol'
        ));
        $this->insert('dataset_attributes', array(
            'id' => '2436',
            'dataset_id' =>'29',
            'attribute_id' => '497',
            'value' => 'http://climb.genomics.cn/10.5524/100002'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE dataset_attributes_id_seq CASCADE;')->execute();
        $this->dropTable('dataset_attributes');
    }
}
