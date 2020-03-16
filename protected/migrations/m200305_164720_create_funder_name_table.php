<?php

class m200305_164720_create_funder_name_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE funder_name (
                id integer NOT NULL,
                uri character varying(100) NOT NULL,
                primary_name_display character varying(1000),
                country character varying(128) DEFAULT \'\'::character varying);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE funder_name_id_seq
                START WITH 6200
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE funder_name_id_seq 
                OWNED BY funder_name.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY funder_name 
                ALTER COLUMN id SET DEFAULT nextval(\'funder_name_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY funder_name
                ADD CONSTRAINT funder_name_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('funder_name', array(
            'id' => '6171',
            'uri' =>'http://dx.doi.org/10.13039/100008363',
            'primary_name_display' => 'The Danish Cancer Society'
        ));
        $this->insert('funder_name', array(
            'id' => '6166',
            'uri' =>'http://www.973.gov.cn/English/Index.aspx',
            'primary_name_display' => 'State Key Development Program for Basic Research of China-973 Program',
            'country' => 'China'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE funder_name_id_seq CASCADE;')->execute();
        $this->dropTable('funder_name');
    }
}
