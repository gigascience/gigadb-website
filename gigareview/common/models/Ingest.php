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

    const REPORT_TYPES = [ "manuscripts" => 1, "authors" => 2,"reviewers" => 3,"questions" => 4,"reviews" => 5];

    const FETCH_STATUS_FOUND = 1;
    const FETCH_STATUS_DOWNLOADED = 2;
    const FETCH_STATUS_DISPATCHED = 3;
    const FETCH_STATUS_ERROR = 0 ;

    const PARSE_STATUS_YES = 1;
    const PARSE_STATUS_NO = 0;

    const REMOTE_FILES_STATUS_EXISTS = 1;
    const REMOTE_FILES_STATUS_NO_RESULTS = 0;

    const STORE_STATUS_YES = 1;
    const STORE_STATUS_NO = 0;

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

    /**
     * Create Ingest instance
     *
     * @param string $reportFileName
     * @param string $scope
     * @return Ingest
     */
    public static function createIngestInstance(string $reportFileName, string $scope): Ingest
    {
        $ingest = new Ingest();
        $ingest->file_name = $reportFileName;
        $ingest->report_type = self::REPORT_TYPES[$scope];
        return $ingest;
    }

    /**
     * Update status in ingest table after save successfully
     *
     * @param string $reportFileName
     * @param string $scope
     * @return bool
     */
    public static function logStatusAfterSave(string $reportFileName, string $scope): bool
    {
        $ingest = self::createIngestInstance($reportFileName, $scope);
        $ingest->fetch_status = self::FETCH_STATUS_DISPATCHED;
        $ingest->remote_file_status = self::REMOTE_FILES_STATUS_EXISTS;
        $ingest->parse_status = self::PARSE_STATUS_YES;
        $ingest->store_status = self::STORE_STATUS_YES;
        return $ingest->save();

    }

    /**
     * Update status in ingest table if fail to save
     *
     * @param string $reportFileName
     * @param string $scope
     * @return bool
     */
    public static function logStatusFailSave(string $reportFileName, string $scope): bool
    {
        $ingest = self::createIngestInstance($reportFileName, $scope);
        $ingest->fetch_status = self::FETCH_STATUS_DISPATCHED;
        $ingest->remote_file_status = self::REMOTE_FILES_STATUS_EXISTS;
        $ingest->parse_status = self::PARSE_STATUS_YES;
        $ingest->store_status = self::STORE_STATUS_NO;
        return $ingest->save();
    }

    /**
     * Update statuses in ingest table for no results EM report
     *
     * @param string $reportFileName
     * @param string $scope
     * @return bool
     */
    public static function logNoResultsReportStatus(string $reportFileName, string $scope): bool
    {
        $ingest = self::createIngestInstance($reportFileName, $scope);
        $ingest->fetch_status = self::FETCH_STATUS_DISPATCHED;
        $ingest->remote_file_status = self::REMOTE_FILES_STATUS_NO_RESULTS;
        $ingest->parse_status = self::PARSE_STATUS_NO;
        $ingest->store_status = self::STORE_STATUS_NO;
        return $ingest->save();
    }
}
