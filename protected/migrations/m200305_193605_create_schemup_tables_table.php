<?php

class m200305_193605_create_schemup_tables_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE schemup_tables (
                table_name character varying NOT NULL,
                version character varying NOT NULL,
                is_current boolean DEFAULT false NOT NULL,
                schema text
);'
        );

        Yii::app()->db->createCommand($sql_createtab)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('schemup_tables', array(
            'table_name' => 'publisher',
            'version' =>'sum_1',
            'is_current' => 't',
            'schema' => 'description|text|NO|\'\'::text'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('schemup_tables');
    }
}
