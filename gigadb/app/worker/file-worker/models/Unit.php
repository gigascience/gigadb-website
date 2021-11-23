<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unit".
 *
 * @property string $id the ID from the unit ontology
 * @property string|null $name the name of the unit (taken from the Unit Ontology)
 * @property string|null $definition the inition taken from the unit ontology
 *
 * @property DatasetAttributes[] $datasetAttributes
 * @property ExpAttributes[] $expAttributes
 * @property FileAttributes[] $fileAttributes
 * @property SampleAttribute[] $sampleAttributes
 */
class Unit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'string', 'max' => 30],
            [['name'], 'string', 'max' => 200],
            [['definition'], 'string', 'max' => 500],
            [['id'], 'unique'],
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
            'definition' => 'Definition',
        ];
    }

    /**
     * Gets query for [[DatasetAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetAttributes()
    {
        return $this->hasMany(DatasetAttributes::className(), ['units_id' => 'id']);
    }

    /**
     * Gets query for [[ExpAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpAttributes()
    {
        return $this->hasMany(ExpAttributes::className(), ['units_id' => 'id']);
    }

    /**
     * Gets query for [[FileAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileAttributes()
    {
        return $this->hasMany(FileAttributes::className(), ['unit_id' => 'id']);
    }

    /**
     * Gets query for [[SampleAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSampleAttributes()
    {
        return $this->hasMany(SampleAttribute::className(), ['unit_id' => 'id']);
    }
}
