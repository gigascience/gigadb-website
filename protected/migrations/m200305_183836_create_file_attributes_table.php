<?php

class m200305_183836_create_file_attributes_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE file_attributes (
                id integer NOT NULL,
                file_id integer NOT NULL,
                attribute_id integer NOT NULL,
                value character varying(1000),
                unit_id character varying(30));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE file_attributes_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE file_attributes_id_seq 
                OWNED BY file_attributes.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY file_attributes 
                ALTER COLUMN id SET DEFAULT nextval(\'file_attributes_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY file_attributes
                ADD CONSTRAINT file_attributes_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY file_attributes
                ADD CONSTRAINT file_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) 
                REFERENCES attribute(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY file_attributes
                ADD CONSTRAINT file_attributes_file_id_fkey FOREIGN KEY (file_id) 
                REFERENCES file(id) ON DELETE CASCADE;'
        );

        $sql_altertab5 = sprintf(
            'ALTER TABLE ONLY file_attributes
                ADD CONSTRAINT file_attributes_unit_id_fkey FOREIGN KEY (unit_id) 
                REFERENCES unit(id);'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4, $sql_altertab5);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('file_attributes', array(
            'id' => '9831',
            'file_id' =>'4328',
            'attribute_id' => '605',
            'value' => 'feb40a51c32a0f3ef3124ad0c05ad824'
        ));
        $this->insert('file_attributes', array(
            'id' => '9822',
            'file_id' =>'4329',
            'attribute_id' => '605',
            'value' => '9cc180feee23eb506e28e08f888cdc56'
        ));
        $this->insert('file_attributes', array(
            'id' => '9836',
            'file_id' =>'6205',
            'attribute_id' => '605',
            'value' => '1a666f2b9937c37479845bb6ae516eac'
        ));
        $this->insert('file_attributes', array(
            'id' => '9837',
            'file_id' =>'6204',
            'attribute_id' => '605',
            'value' => '84eb68fecad964b31128d3a27ee77729'
        ));
        $this->insert('file_attributes', array(
            'id' => '9839',
            'file_id' =>'6201',
            'attribute_id' => '605',
            'value' => '4c7f5b5676d7db69bc8581cce9face1d'
        ));
        $this->insert('file_attributes', array(
            'id' => '9835',
            'file_id' =>'6203',
            'attribute_id' => '605',
            'value' => '7e44c4e01ec74a89202c505eb731ded8'
        ));
        $this->insert('file_attributes', array(
            'id' => '9846',
            'file_id' =>'4298',
            'attribute_id' => '605',
            'value' => '246c1c9a13dc5c5810cc0bac66f3f538'
        ));
        $this->insert('file_attributes', array(
            'id' => '9861',
            'file_id' =>'4281',
            'attribute_id' => '605',
            'value' => '4960e8ce258200b614f5556b184862f9'
        ));
        $this->insert('file_attributes', array(
            'id' => '9868',
            'file_id' =>'4286',
            'attribute_id' => '605',
            'value' => '8a53b61fb22ecf5d9eb5e066f41de1a6'
        ));
        $this->insert('file_attributes', array(
            'id' => '9856',
            'file_id' =>'4289',
            'attribute_id' => '605',
            'value' => '78e8864f9496210753494d8b1d52761c'
        ));
        $this->insert('file_attributes', array(
            'id' => '10513',
            'file_id' =>'5993',
            'attribute_id' => '605',
            'value' => '6e9bf1e4fa5a7e724ec4020a27253f2b'
        ));
        $this->insert('file_attributes', array(
            'id' => '10660',
            'file_id' =>'5717',
            'attribute_id' => '605',
            'value' => 'f369f3da72bbf6d9e33a7bf5f1c8c9ce'
        ));
        $this->insert('file_attributes', array(
            'id' => '10309',
            'file_id' =>'5937',
            'attribute_id' => '605',
            'value' => 'b5dbbcf80ea5dee1c46e2a2af315e592'
        ));
        $this->insert('file_attributes', array(
            'id' => '10655',
            'file_id' =>'5721',
            'attribute_id' => '605',
            'value' => 'fc98a063f301e4a5a2352b7374d329c6'
        ));

    }

    public function safeDown()
    {
        $this->dropTable('file_attributes');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE file_attributes_id_seq;')->execute();
    }
}
