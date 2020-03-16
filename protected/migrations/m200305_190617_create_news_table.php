<?php

class m200305_190617_create_news_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE news (
                id integer NOT NULL,
                title character varying(200) NOT NULL,
                body text DEFAULT \'\'::text NOT NULL,
                start_date date NOT NULL,
                end_date date NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE news_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE news_id_seq 
                OWNED BY news.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY news 
                ALTER COLUMN id SET DEFAULT nextval(\'news_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY news
                ADD CONSTRAINT news_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('news', array(
            'id' => '3',
            'title' =>'New features coming soon',
            'body' => 'Check back at the end of January to see new features on this website!\r+',
            'start_date' => '2015-12-04',
            'end_date' => '2015-12-23'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE news_id_seq CASCADE;')->execute();
        $this->dropTable('news');
    }
}
