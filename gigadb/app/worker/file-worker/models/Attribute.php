<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attribute".
 *
 * @property int $id
 * @property string|null $attribute_name
 * @property string|null $definition
 * @property string|null $model
 * @property string|null $structured_comment_name
 * @property string|null $value_syntax
 * @property string|null $allowed_units
 * @property string|null $occurance
 * @property string|null $ontology_link
 * @property string|null $note
 *
 * @property DatasetAttributes[] $datasetAttributes
 * @property ExpAttributes[] $expAttributes
 * @property FileAttributes[] $fileAttributes
 * @property SampleAttribute[] $sampleAttributes
 */
class Attribute extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attribute';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_name', 'model', 'structured_comment_name', 'allowed_units', 'note'], 'string', 'max' => 100],
            [['definition', 'ontology_link'], 'string', 'max' => 1000],
            [['value_syntax'], 'string', 'max' => 500],
            [['occurance'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_name' => 'Attribute Name',
            'definition' => 'Definition',
            'model' => 'Model',
            'structured_comment_name' => 'Structured Comment Name',
            'value_syntax' => 'Value Syntax',
            'allowed_units' => 'Allowed Units',
            'occurance' => 'Occurance',
            'ontology_link' => 'Ontology Link',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[DatasetAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetAttributes()
    {
        return $this->hasMany(DatasetAttributes::className(), ['attribute_id' => 'id']);
    }

    /**
     * Gets query for [[ExpAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpAttributes()
    {
        return $this->hasMany(ExpAttributes::className(), ['attribute_id' => 'id']);
    }

    /**
     * Gets query for [[FileAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileAttributes()
    {
        return $this->hasMany(FileAttributes::className(), ['attribute_id' => 'id']);
    }

    /**
     * Gets query for [[SampleAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSampleAttributes()
    {
        return $this->hasMany(SampleAttribute::className(), ['attribute_id' => 'id']);
    }
}
