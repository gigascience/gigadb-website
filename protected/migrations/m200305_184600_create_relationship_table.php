<?php

class m200305_184600_create_relationship_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE relationship (
                id integer NOT NULL,
                name character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE relationship_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY relationship 
                ALTER COLUMN id SET DEFAULT nextval(\'relationship_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY relationship
                ADD CONSTRAINT relationship_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('relationship', array(
            'id' => '1',
            'name' => 'IsCitedBy'
        ));
        $this->insert('relationship', array(
            'id' => '2',
            'name' => 'Cites'
        ));
        $this->insert('relationship', array(
            'id' => '3',
            'name' => 'IsSupplementTo'
        ));
        $this->insert('relationship', array(
            'id' => '4',
            'name' => 'IsSupplementedBy'
        ));
        $this->insert('relationship', array(
            'id' => '5',
            'name' => 'IsContinuedBy'
        ));
        $this->insert('relationship', array(
            'id' => '6',
            'name' => 'Continues'
        ));
        $this->insert('relationship', array(
            'id' => '7',
            'name' => 'HasMetadata'
        ));
        $this->insert('relationship', array(
            'id' => '8',
            'name' => 'IsMetadataFor'
        ));
        $this->insert('relationship', array(
            'id' => '9',
            'name' => 'IsNewVersionOf'
        ));
        $this->insert('relationship', array(
            'id' => '10',
            'name' => 'IsPreviousVersionOf'
        ));
        $this->insert('relationship', array(
            'id' => '11',
            'name' => 'IsPartOf'
        ));
        $this->insert('relationship', array(
            'id' => '12',
            'name' => 'HasPart'
        ));
        $this->insert('relationship', array(
            'id' => '13',
            'name' => 'IsReferencedBy'
        ));
        $this->insert('relationship', array(
            'id' => '14',
            'name' => 'References'
        ));
        $this->insert('relationship', array(
            'id' => '15',
            'name' => 'IsDocumentedBy'
        ));
        $this->insert('relationship', array(
            'id' => '16',
            'name' => 'Documents'
        ));
        $this->insert('relationship', array(
            'id' => '17',
            'name' => 'IsCompiledBy'
        ));
        $this->insert('relationship', array(
            'id' => '18',
            'name' => 'Compiles'
        ));
        $this->insert('relationship', array(
            'id' => '19',
            'name' => 'IsVariantFormOf'
        ));
        $this->insert('relationship', array(
            'id' => '20',
            'name' => 'IsOriginalFormOf'
        ));
        $this->insert('relationship', array(
            'id' => '21',
            'name' => 'IsIdenticalTo'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE relationship_id_seq CASCADE;')->execute();
        $this->dropTable('relationship');
    }
}
