<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "manuscript".
 *
 * @property int $id
 * @property string|null $manuscript_number
 * @property string|null $article_title
 * @property int|null $revision_number
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Manuscript extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manuscript';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['revision_number', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['revision_number', 'created_at', 'updated_at'], 'integer'],
            [['manuscript_number', 'article_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'manuscript_number' => 'Manuscript Number',
            'article_title' => 'Article Title',
            'revision_number' => 'Revision Number',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
