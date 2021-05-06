<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "file_format".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $edam_ontology_id
 *
 * @property File[] $files
 */
class FileFormat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_format';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 20],
            [['edam_ontology_id'], 'string', 'max' => 100],
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

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::className(), ['format_id' => 'id']);
    }
}
