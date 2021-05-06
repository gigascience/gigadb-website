<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "attribute".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $value
 * @property string|null $unit
 * @property int $upload_id
 *
 * @property Upload $upload
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
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
            [['upload_id'], 'required'],
            [['upload_id'], 'default', 'value' => null],
            [['upload_id'], 'integer'],
            [['name', 'value', 'unit'], 'string', 'max' => 255],
            [['upload_id'], 'exist', 'skipOnError' => true, 'targetClass' => Upload::className(), 'targetAttribute' => ['upload_id' => 'id']],
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
            'value' => 'Value',
            'unit' => 'Unit',
            'upload_id' => 'Upload ID',
        ];
    }

    /**
     * Gets query for [[Upload]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpload()
    {
        return $this->hasOne(Upload::className(), ['id' => 'upload_id']);
    }
}
