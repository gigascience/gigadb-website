<?php

namespace GigaDB\models;

use Yii;
use yii\db\Connection;

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

    /**
     * @param $datasetId
     * @param Connection|null $db
     * @return array
     * @throws \CException
     */
    public static function listByDatasetId(int $datasetId, $db = null): array
    {

        $db = $db ?? Yii::$app->db;

        $sql = "select a.id, a.surname, a.first_name, a.middle_name, a.custom_name from author a, dataset_author da, dataset d where a.id=da.author_id and d.id = da.dataset_id and d.id=:id order by rank ASC, a.surname ASC, a.first_name ASC, a.middle_name ASC";

        $command = $db->createCommand($sql);
        $command->bindParam(":id", $datasetId, \PDO::PARAM_INT);

        // Fetch all results
        return $command->queryAll();
    }
}
