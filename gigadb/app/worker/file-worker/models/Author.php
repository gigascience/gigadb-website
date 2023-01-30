<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "author".
 *
 * @property int $id
 * @property string $surname
 * @property string|null $middle_name
 * @property string|null $first_name
 * @property string|null $orcid
 * @property int|null $gigadb_user_id
 * @property string|null $custom_name
 *
 * @property DatasetAuthor[] $datasetAuthors
 */
class Author extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surname'], 'required'],
            [['gigadb_user_id'], 'default', 'value' => null],
            [['gigadb_user_id'], 'integer'],
            [['surname', 'middle_name', 'first_name', 'orcid'], 'string', 'max' => 255],
            [['custom_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'surname' => 'Surname',
            'middle_name' => 'Middle Name',
            'first_name' => 'First Name',
            'orcid' => 'Orcid',
            'gigadb_user_id' => 'Gigadb User ID',
            'custom_name' => 'Custom Name',
        ];
    }

    /**
     * Gets query for [[DatasetAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatasetAuthors()
    {
        return $this->hasMany(DatasetAuthor::class, ['author_id' => 'id']);
    }
}
