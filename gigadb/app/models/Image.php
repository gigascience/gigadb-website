<?php

namespace GigaDB\models;

use Yii;

/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property string $location
 * @property string|null $tag
 * @property string|null $url
 * @property string $license
 * @property string $photographer
 * @property string $source
 *
 * @property Dataset[] $datasets
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['license'], 'string'],
            [['location'], 'string', 'max' => 200],
            [['tag'], 'string', 'max' => 300],
            [['url', 'source'], 'string', 'max' => 256],
            [['photographer'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'location' => 'Location',
            'tag' => 'Tag',
            'url' => 'Url',
            'license' => 'License',
            'photographer' => 'Photographer',
            'source' => 'Source',
        ];
    }

    /**
     * Gets query for [[Datasets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasets()
    {
        return $this->hasMany(Dataset::class, ['image_id' => 'id']);
    }
}
