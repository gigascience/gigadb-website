<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attribute}}`.
 */
class m200212_152222_create_attribute_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%attribute}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'value' => $this->string(),
            'unit' => $this->string(),
            'upload_id' => $this->integer()->notNull()
        ]);

        // comment below because of unresolved https://github.com/yiisoft/yii2/issues/13102  
        // // creates index for column `upload_id`
        // $this->createIndex(
        //     'idx-attribute-upload_id',
        //     '{{%attribute}}',
        //     'upload_id'
        // );

        // // add foreign key for table `upload`
        // $this->addForeignKey(
        //     'fk-attribute-upload_id',
        //     '{{%attribute}}',
        //     'upload_id',
        //     'upload',
        //     'id',
        //     'CASCADE'
        // );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        // // drops foreign key for table `upload`
        // $this->dropForeignKey(
        //     'fk-attribute-upload_id',
        //     '{{%attribute}}'
        // );

        // // drops index for column `upload_id`
        // $this->dropIndex(
        //     'idx-attribute-upload_id',
        //     '{{%attribute}}'
        // );

        $this->dropTable('{{%attribute}}');
    }
}
