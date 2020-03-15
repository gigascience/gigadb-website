<?php

class m200305_164525_create_dataset_author_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset_author (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                author_id integer NOT NULL,
                rank integer DEFAULT 0,
                role character varying(30));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_author_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_author_id_seq 
                OWNED BY dataset_author.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY dataset_author 
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_author_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY dataset_author
                ADD CONSTRAINT dataset_author_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY dataset_author
                ADD CONSTRAINT dataset_author_author_id_fkey FOREIGN KEY (author_id) 
                REFERENCES author(id) ON DELETE CASCADE;'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY dataset_author
                ADD CONSTRAINT dataset_author_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('dataset_author', array(
            'id' => '2',
            'dataset_id' => '15',
            'author_id' => '566',
            'rank' => '1'
        ));
        $this->insert('dataset_author', array(
            'id' => '3',
            'dataset_id' => '15',
            'author_id' => '567',
            'rank' => '2'
        ));
        $this->insert('dataset_author', array(
            'id' => '4',
            'dataset_id' => '15',
            'author_id' => '568',
            'rank' => '3'
        ));
        $this->insert('dataset_author', array(
            'id' => '5',
            'dataset_id' => '15',
            'author_id' => '569',
            'rank' => '4'
        ));

        $this->insert('dataset_author', array(
            'id' => '35',
            'dataset_id' => '29',
            'author_id' => '3305',
            'rank' => '1'
        ));
        $this->insert('dataset_author', array(
            'id' => '36',
            'dataset_id' => '29',
            'author_id' => '3325',
            'rank' => '2'
        ));
        $this->insert('dataset_author', array(
            'id' => '37',
            'dataset_id' => '29',
            'author_id' => '3062',
            'rank' => '3'
        ));
        $this->insert('dataset_author', array(
            'id' => '38',
            'dataset_id' => '29',
            'author_id' => '3337',
            'rank' => '4'
        ));

        $this->insert('dataset_author', array(
            'id' => '80',
            'dataset_id' => '13',
            'author_id' => '3305',
            'rank' => '1'
        ));
        $this->insert('dataset_author', array(
            'id' => '81',
            'dataset_id' => '13',
            'author_id' => '3325',
            'rank' => '2'
        ));
        $this->insert('dataset_author', array(
            'id' => '82',
            'dataset_id' => '13',
            'author_id' => '3062',
            'rank' => '3'
        ));
        $this->insert('dataset_author', array(
            'id' => '83',
            'dataset_id' => '13',
            'author_id' => '3337',
            'rank' => '4'
        ));

        $this->insert('dataset_author', array(
            'id' => '125',
            'dataset_id' => '25',
            'author_id' => '3155',
            'rank' => '1'
        ));
        $this->insert('dataset_author', array(
            'id' => '126',
            'dataset_id' => '25',
            'author_id' => '3063',
            'rank' => '2'
        ));
        $this->insert('dataset_author', array(
            'id' => '127',
            'dataset_id' => '25',
            'author_id' => '3245',
            'rank' => '3'
        ));
        $this->insert('dataset_author', array(
            'id' => '128',
            'dataset_id' => '25',
            'author_id' => '3357',
            'rank' => '4'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('dataset_author');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE dataset_author_id_seq;')->execute();
    }
}
