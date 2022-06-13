<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ingest".
 *
 * @property int $id
 * @property string|null $file_name
 * @property int|null $report_type
 * @property int|null $fetch_status
 * @property int|null $parse_status
 * @property int|null $store_status
 * @property int|null $remote_file_status
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Ingest extends \yii\db\ActiveRecord
{

    const FETCH_STATUS_FOUND = 1;
    const FETCH_STATUS_DOWNLOADED = 2;
    const FETCH_STATUS_DISPATCHED = 3;
    const FETCH_STATUS_ERROR = 0 ;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ingest';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['report_type', 'fetch_status', 'parse_status', 'store_status', 'remote_file_status'], 'default', 'value' => null],
            [['report_type', 'fetch_status', 'parse_status', 'store_status', 'remote_file_status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['file_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_name' => 'File Name',
            'report_type' => 'Report Type',
            'fetch_status' => 'Fetch Status',
            'parse_status' => 'Parse Status',
            'store_status' => 'Store Status',
            'remote_file_status' => 'Remote File Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
