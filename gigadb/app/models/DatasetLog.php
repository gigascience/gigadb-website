<?php

declare(strict_types=1);

namespace GigaDB\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 *
 * @property int $id
 * @property int $dataset_id
 * @property string|null $message
 * @property string|null $created_at
 * @property string|null $model
 * @property string|null $model_id
 * @property string|null $url
 *
 * @property Dataset $dataset
 */
class DatasetLog extends ActiveRecord
{
    public $doi;

    public static function tableName(): string
    {
        return 'dataset_log';
    }

    public function rules(): array
    {
        return [
            [['dataset_id', 'message'], 'required'],
            [['dataset_id', 'model_id'], 'integer'],
            [['message', 'created_at', 'model', 'model_id'], 'safe'],
            [['id', 'dataset_id', 'message', 'created_at', 'model', 'model_id'], 'safe', 'on' => 'search'],
            [['model'], 'match', 'pattern' => '/^[a-zA-Z]+$/', 'message' => 'Only letters are allowed']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'dataset_id' => 'Dataset',
            'message'    => 'Message',
            'created_at' => 'Created At',
            'model'      => 'Table Changed',
            'model_id'   => 'Table Row',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function getDataset()
    {
        return $this->hasOne(Dataset::class, ['id' => 'dataset_id']);
    }

    public static function makeNewInstanceForDatasetLogBy(
        int    $datasetId,
        string $fileName,
        ?string $fileModel = null,
        ?int    $modelId = null,
        ?int   $fileId = null
    ): self {
        $datasetLog = new self();
        $datasetLog->dataset_id = $datasetId;
        $datasetLog->message = $fileName;
        $datasetLog->model = $fileModel;
        $datasetLog->model_id = $modelId;

        if ($fileId) {
            $datasetLog->url = \Yii::$app->urlManager->createUrl(['/admin-file/update', 'id' => $fileId]);
        }

        return $datasetLog;
    }

    public static function createDatasetLogEntry(
        int    $datasetId,
        string $fileName,
        ?string $fileModel = null,
        ?int    $modelId = null,
        ?int    $fileId = null
    ): bool {
        $datasetLog = self::makeNewInstanceForDatasetLogBy($datasetId, $fileName, $fileModel, $modelId, $fileId);

        return $datasetLog->save();
    }

    public function search(array $params)
    {
        $query = self::find()->joinWith('dataset');

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['dataset_id' => $this->dataset_id]);
        $query->andFilterWhere(['like', 'message', $this->message]);
        $query->andFilterWhere(['like', 'created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'model', $this->model]);
        $query->andFilterWhere(['like', 'model_id', $this->model_id]);
        $query->andFilterWhere(['like', 'dataset.identifier', $this->doi]);

        return $dataProvider;
    }
}
