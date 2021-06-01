<?php

namespace app\controllers;

use \yii\helpers\Console;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * The tool for updating production database's file table to replace ftp urls
 * @package app\commands
 */
class DatasetFilesController extends Controller
{
    /**
     * @var string $date the yyyymmdd for which to retrieve a production backup
     */
    public $date;

    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help','date'];
    }

    /**
     * This command will download and load in database production backup for the given date
     *
     * @return int Exit code
     */
    public function actionDownloadRestoreBackup()
    {
        $this->stdout("Downloading and restoring the backup for {$this->date}\n", Console::BOLD);
        system("echo true");
        return ExitCode::OK;
    }

    /**
     * This command will list the dataset with files in need of updating for ftp urls replacement
     *
     * TODO: to implement
     * @return int Exit code
     */
    public function actionListPendingDatasets()
    {
        return ExitCode::OK;
    }

    /**
     * This command will update file table to replace ftp urls for the supplied list of dataset ids
     *
     * TODO: to implement
     * @param array $dataset_ids the message to be echoed.
     * @return int Exit code
     */
    public function actionUpdateFtpUrl(array $dataset_ids)
    {
        return ExitCode::OK;
    }

}
