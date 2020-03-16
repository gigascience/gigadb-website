<?php

class m200305_183134_create_external_link_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE external_link (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                url character varying(300) NOT NULL,
                external_link_type_id integer NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE external_link_id_seq
                START WITH 30
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE external_link_id_seq 
                OWNED BY external_link.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY external_link 
                ALTER COLUMN id SET DEFAULT nextval(\'external_link_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY external_link
                ADD CONSTRAINT external_link_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY external_link
                ADD CONSTRAINT external_link_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY external_link
                ADD CONSTRAINT external_link_external_link_type_id_fkey FOREIGN KEY (external_link_type_id) 
                REFERENCES external_link_type(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('external_link', array(
            'id' => '5',
            'dataset_id' =>'13',
            'url' => 'http://macaque.genomics.org.cn/',
            'external_link_type_id' => '2'
        ));
        $this->insert('external_link', array(
            'id' => '8',
            'dataset_id' =>'15',
            'url' => 'https://github.com/ehec-outbreak-crowdsourced/BGI-data-analysis/wiki/',
            'external_link_type_id' => '1'
        ));
        $this->insert('external_link', array(
            'id' => '14',
            'dataset_id' =>'25',
            'url' => 'http://panda.genomics.org.cn/',
            'external_link_type_id' => '2'
        ));
        $this->insert('external_link', array(
            'id' => '16',
            'dataset_id' =>'29',
            'url' => 'http://macaque.genomics.org.cn/',
            'external_link_type_id' => '2'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE external_link_id_seq CASCADE;')->execute();
        $this->dropTable('external_link');
    }
}
