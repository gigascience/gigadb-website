<?php

class m200305_160724_create_AuthAssignment_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE "AuthAssignment" (
                itemname character varying(64) NOT NULL,
                userid character varying(64) NOT NULL,
                bizrule text,
                data text);'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY "AuthAssignment"
                ADD CONSTRAINT "AuthAssignment_pkey" PRIMARY KEY (itemname, userid);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY "AuthAssignment"
                ADD CONSTRAINT "AuthAssignment_itemname_fkey" FOREIGN KEY (itemname) REFERENCES "AuthItem"(name) ON UPDATE CASCADE ON DELETE CASCADE;'
        );

        $sql_cmds = array( $sql_createtab, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('AuthAssignment');
    }
}
