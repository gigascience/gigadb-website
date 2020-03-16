<?php

class m200305_174022_create_dataset_type_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset_type (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                type_id integer);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_type_id_seq
                START WITH 37
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_type_id_seq 
                OWNED BY dataset_type.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY dataset_type 
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_type_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY dataset_type
                ADD CONSTRAINT dataset_type_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY dataset_type
                ADD CONSTRAINT dataset_type_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY dataset_type
                ADD CONSTRAINT dataset_type_type_id_fkey FOREIGN KEY (type_id) 
                REFERENCES type(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('dataset_type', array(
            'id' => '18',
            'dataset_id' => '15',
            'type_id' =>'2'
        ));
        $this->insert('dataset_type', array(
            'id' => '32',
            'dataset_id' => '29',
            'type_id' =>'2'
        ));
        $this->insert('dataset_type', array(
            'id' => '14',
            'dataset_id' => '13',
            'type_id' =>'2'
        ));
        $this->insert('dataset_type', array(
            'id' => '15',
            'dataset_id' => '13',
            'type_id' =>'4'
        ));
        $this->insert('dataset_type', array(
            'id' => '28',
            'dataset_id' => '25',
            'type_id' =>'2'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE dataset_type_id_seq CASCADE;')->execute();
        $this->dropTable('dataset_type');
    }
}
