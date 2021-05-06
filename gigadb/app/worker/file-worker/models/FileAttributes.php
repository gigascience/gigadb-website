<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "file_attributes".
 *
 * @property int $id
 * @property int $file_id
 * @property int $attribute_id
 * @property string|null $value
 * @property string|null $unit_id
 *
 * @property Attribute $attribute0
 * @property File $file
 * @property Unit $unit
 */
class FileAttributes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_attributes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file_id', 'attribute_id'], 'required'],
            [['file_id', 'attribute_id'], 'default', 'value' => null],
            [['file_id', 'attribute_id'], 'integer'],
            [['value'], 'string', 'max' => 200],
            [['unit_id'], 'string', 'max' => 30],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attribute::className(), 'targetAttribute' => ['attribute_id' => 'id']],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_id' => 'File ID',
            'attribute_id' => 'Attribute ID',
            'value' => 'Value',
            'unit_id' => 'Unit ID',
        ];
    }

    /**
     * Gets query for [[Attribute0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttribute0()
    {
        return $this->hasOne(Attribute::className(), ['id' => 'attribute_id']);
    }

    /**
     * Gets query for [[File]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(File::className(), ['id' => 'file_id']);
    }

    /**
     * Gets query for [[Unit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit_id']);
    }
}
