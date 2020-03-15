<?php

class m200305_164810_create_dataset_funder_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset_funder (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                funder_id integer NOT NULL,
                grant_award text DEFAULT \'\'::text,
                comments text DEFAULT \'\'::text,
                awardee character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_funder_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_funder_id_seq 
                OWNED BY dataset_funder.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY dataset_funder 
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_funder_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY dataset_funder
                ADD CONSTRAINT dataset_funder_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY dataset_funder
                ADD CONSTRAINT dataset_funder_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY dataset_funder
                ADD CONSTRAINT dataset_funder_funder_id_fkey FOREIGN KEY (funder_id) 
                REFERENCES funder_name(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('dataset_funder', array(
            'id' => '29',
            'dataset_id' =>'204',
            'funder_id' => '6171'
        ));
        $this->insert('dataset_funder', array(
            'id' => '25',
            'dataset_id' =>'41',
            'funder_id' => '6166',
            'grant_award' => '2011CB809203'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('dataset_funder');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE dataset_funder_id_seq;')->execute();
    }
}
