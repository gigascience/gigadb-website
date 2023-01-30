<?php

namespace app\models;

/**
 * This is the model class for table "dataset_author".
 *
 * @property int $id
 * @property int $dataset_id
 * @property int $author_id
 * @property int|null $rank
 * @property string|null $role
 *
 * @property Author $author
 * @property Dataset $dataset
 */
class DatasetAuthor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dataset_author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dataset_id', 'author_id'], 'required'],
            [['dataset_id', 'author_id', 'rank'], 'default', 'value' => null],
            [['dataset_id', 'author_id', 'rank'], 'integer'],
            [['role'], 'string', 'max' => 30],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            [['dataset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dataset::class, 'targetAttribute' => ['dataset_id' => 'id']],
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
            'author_id' => 'Author ID',
            'rank' => 'Rank',
            'role' => 'Role',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
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
}
