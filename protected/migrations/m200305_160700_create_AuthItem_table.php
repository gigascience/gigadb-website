<?php

class m200305_160700_create_AuthItem_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE "AuthItem" (
                name character varying(64) NOT NULL,
                type integer NOT NULL,
                description text,
                bizrule text,
                data text);'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY "AuthItem"
                ADD CONSTRAINT "AuthItem_pkey" PRIMARY KEY (name);'
        );

        $sql_cmds = array($sql_createtab, $sql_altertab1);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('AuthItem');
    }
}
