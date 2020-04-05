<?php

class m200305_163621_create_author_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE author (
                id integer NOT NULL,
                surname character varying(255) NOT NULL,
                middle_name character varying(255),
                first_name character varying(255),
                orcid character varying(255),
                gigadb_user_id integer,
                custom_name character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE author_id_seq
                START WITH 3500
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE author_id_seq 
                OWNED BY author.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY author 
                ALTER COLUMN id SET DEFAULT nextval(\'author_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY author
                ADD CONSTRAINT author_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('author', array(
            'id' => '566',
            'surname' =>'Li',
            'first_name' => 'Dongfang'
        ));
        $this->insert('author', array(
            'id' => '567',
            'surname' =>'Xi',
            'first_name' => 'Feng'
        ));
        $this->insert('author', array(
            'id' => '568',
            'surname' =>'Zhao',
            'first_name' => 'Meiru'
        ));
        $this->insert('author', array(
            'id' => '569',
            'surname' =>'Chen',
            'first_name' => 'Wentong'
        ));
        $this->insert('author', array(
            'id' => '2630',
            'surname' =>'Vattikuti',
            'first_name' => 'Shashaank'
        ));
        $this->insert('author', array(
            'id' => '2631',
            'surname' =>'Lee',
            'middle_name' => 'J',
            'first_name' => 'James'
        ));
        $this->insert('author', array(
            'id' => '2632',
            'surname' =>'Chang',
            'middle_name' => 'C',
            'first_name' => 'Chris'
        ));
        $this->insert('author', array(
            'id' => '2633',
            'surname' =>'Hsu',
            'middle_name' => 'DH',
            'first_name' => 'S'
        ));
        $this->insert('author', array(
            'id' => '2633',
            'surname' =>'Chow',
            'middle_name' => 'C',
            'first_name' => 'Carson'
        ));
        $this->insert('author', array(
            'id' => '3305',
            'surname' =>'Yan',
            'first_name' => 'Guangmei'
        ));
        $this->insert('author', array(
            'id' => '3325',
            'surname' =>'Zhang',
            'first_name' => 'Guojie'
        ));
        $this->insert('author', array(
            'id' => '3062',
            'surname' =>'Fang',
            'first_name' => 'Xiaodong'
        ));
        $this->insert('author', array(
            'id' => '3063',
            'surname' =>'Fan',
            'first_name' => 'Wei'
        ));
        $this->insert('author', array(
            'id' => '3155',
            'surname' =>'Li',
            'first_name' => 'Ruiqiang'
        ));
        $this->insert('author', array(
            'id' => '3245',
            'surname' =>'Tian',
            'first_name' => 'Geng'
        ));
        $this->insert('author', array(
            'id' => '3325',
            'surname' =>'Zhang',
            'first_name' => 'Guojie'
        ));
        $this->insert('author', array(
            'id' => '3257',
            'surname' =>'Wang',
            'first_name' => 'Jun'
        ));
        $this->insert('author', array(
            'id' => '3337',
            'surname' =>'Zhang',
            'first_name' => 'Yanfeng'
        ));
        $this->insert('author', array(
            'id' => '3371',
            'surname' =>'Lambert',
            'middle_name' => 'M',
            'first_name' => 'David'
        ));
        $this->insert('author', array(
            'id' => '3357',
            'surname' =>'Zhu',
            'first_name' => 'Hongmei'
        ));
        $this->insert('author', array(
            'id' => '3724',
            'surname' =>'Alnasir',
            'first_name' => 'Jamie'
        ));
        $this->insert('author', array(
            'id' => '3725',
            'surname' =>'Shanahan',
            'first_name' => 'Hugh'
        ));
        $this->insert('author', array(
            'id' => '5209',
            'surname' =>'Ni',
            'first_name' => 'Xuemei'
        ));
        $this->insert('author', array(
            'id' => '4402',
            'surname' =>'Xia',
            'first_name' => 'Qiuju'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE author_id_seq CASCADE;')->execute();
        $this->dropTable('author');
    }
}
