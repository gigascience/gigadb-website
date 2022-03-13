<?php

class m220309_165454_generic_image extends CDbMigration
{
       public function safeUp()
       {
           $this->execute("ALTER TABLE ONLY image ALTER COLUMN license SET DEFAULT 'All rights reserved'");
           $this->execute("ALTER TABLE ONLY image ALTER COLUMN photographer SET DEFAULT 'n/a'");
           $this->execute("ALTER TABLE ONLY image ALTER COLUMN source SET DEFAULT 'GigaDB'");
           $this->insert("image", ['id' => 0, 'location' => 'no_image.png', 'url' =>'https://assets.gigadb-cdn.net/images/datasets/no_image.png']);
       }

       public function safeDown()
       {
               $this->delete("image", ['id=0']);
               $this->execute("ALTER TABLE ONLY image ALTER COLUMN license DROP DEFAULT");
               $this->execute("ALTER TABLE ONLY image ALTER COLUMN photographer DROP DEFAULT");
               $this->execute("ALTER TABLE ONLY image ALTER COLUMN source DROP DEFAULT");


       }
}