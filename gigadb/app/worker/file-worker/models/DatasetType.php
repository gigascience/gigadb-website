<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dataset_type".
 *
 * @property int $id
 * @property int $dataset_id
 * @property int|null $type_id
 *
 * @property Dataset $dataset
 * @property Type $type
 */
class DatasetType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dataset_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dataset_id'], 'required'],
            [['dataset_id', 'type_id'], 'default', 'value' => null],
            [['dataset_id', 'type_id'], 'integer'],
            [['dataset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dataset::class, 'targetAttribute' => ['dataset_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dataset_id' => 'Dataset ID',
            'type_id' => 'Type ID',
        ];
    }

    /**
     * Gets query for [[Dataset]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDataset()
    {
        return $this->hasOne(Dataset::class, ['id' => 'dataset_id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['id' => 'type_id']);
    }
}
