<?php

declare(strict_types=1);

namespace GigaDB\models;

use GigaDB\behaviors\DatasetRelatedTableBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * @property int $id
 * @property string $identifier
 * @property int|null $pmid
 * @property int $dataset_id
 * @property bool|null $is_pre_print
 *
 * @property Dataset $dataset
 */
class Manuscript extends ActiveRecord
{
    public ?string $doi_search = null;

    public static function tableName(): string
    {
        return 'manuscript';
    }

    public function rules(): array
    {
        return [
            [['identifier', 'dataset_id'], 'required'],
            [['pmid', 'dataset_id'], 'integer'],
            [['identifier'], 'string', 'max' => 32],
            [['is_pre_print'], 'boolean'],
            [['is_pre_print'], 'default', 'value' => false],
            [['identifier', 'pmid', 'dataset_id', 'doi_search'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'identifier' => 'Identifier',
            'pmid' => 'PMID',
            'dataset_id' => 'Dataset',
            'doi_search' => 'DOI',
        ];
    }

    public function getDataset()
    {
        return $this->hasOne(Dataset::class, ['id' => 'dataset_id']);
    }

    public function getDOILink(): string
    {
        return 'http://dx.doi.org/' . $this->identifier;
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => DatasetRelatedTableBehavior::class,
            ],
        ];
    }

    public function search(): ActiveDataProvider
    {
        $query = self::find()->joinWith('dataset');

        $query->andFilterWhere(['manuscript.id' => $this->id])
            ->andFilterWhere(['LOWER(manuscript.identifier)' => strtolower($this->identifier)])
            ->andFilterWhere(['manuscript.pmid' => $this->pmid])
            ->andFilterWhere(['manuscript.dataset_id' => $this->dataset_id])
            ->andFilterWhere(['LIKE', 'dataset.identifier', $this->doi_search]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
