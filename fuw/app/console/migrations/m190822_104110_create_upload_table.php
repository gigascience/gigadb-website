<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%upload}}`.
 */
class m190822_104110_create_upload_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            // id serial PRIMARY KEY,
            // doi_suffix integer NOT NULL,
            // name character varying(100) NOT NULL,
            // size bigint NOT NULL,
            // status character varying(100) DEFAULT 'uploading'::text, -- 1: uploading, 2: private, 3: public
            // location character varying(200),
            // description text DEFAULT ''::text,
            // initial_md5 text DEFAULT ''::text,
            // format text DEFAULT 'Unknown'::text,
            // data_type text DEFAULT 'Unknown'::text,
        $this->createTable('{{%upload}}', [
            'id' => 'serial PRIMARY KEY',
            'doi' => 'character varying(100) NOT NULL',
            'name' => 'character varying(128) NOT NULL',
            'size' => 'bigint NOT NULL',
            'status' => 'character varying(100)',
            'location' => 'character varying(200)',
            'description' => 'text',
            'initial_md5' => 'text',
            'extension' => 'character varying(32)',
            'created_at' => 'timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL',
            'updated_at' => 'timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL',
        ]);

        $this->execute("CREATE OR REPLACE FUNCTION update_modified_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql'");

        $this->execute("CREATE TRIGGER update_upload_modtime BEFORE UPDATE ON upload
FOR EACH ROW EXECUTE PROCEDURE  update_modified_column()");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%upload}}');
    }
}
