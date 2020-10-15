<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "file_type".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $edam_ontology_id
 */
class FileType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['name', 'edam_ontology_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'edam_ontology_id' => 'Edam Ontology ID',
        ];
    }
}
