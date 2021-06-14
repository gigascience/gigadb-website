<?php

namespace app\controllers;

use \yii\helpers\Console;
use \yii\console\Controller;
use \yii\console\ExitCode;
use app\models\DatasetFiles;

/**
 * The tool for updating production database's file table to replace ftp urls
 * @package app\commands
 */
class DatasetFilesController extends Controller
{
    /**
     * @var string $date the yyyymmdd for which to retrieve a production backup
     */
    public string $date;

    /**
     * @var array $ids list of dataset ids to process
     */
    public array $ids;

    /**
     * @var bool $all if true get all pending datasets
     */
    public bool $all = false;

    /**
     * @var int $next get list of next $next pending datasets
     */
    public int $next = 0;
    /**
     * @var int $after dataset id only pending datasets after this one are returned
     */
    public int $after = 0;

    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help','date','ids','all','next','after'];
    }

    public function init()
    {
        $this->date ??= date('Ymd') - 1 ;
    }


    /**
     * This command will download and load in database production backup for the given date
     *
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionDownloadRestoreBackup()
    {
        $dbConfig = \Yii::$app->db->attributes;
        $dbUser = \Yii::$app->db->username;
        $dbPassword = \Yii::$app->db->password;
        $ftpConfig = \Yii::$app->params['ftp'];

        $this->stdout("\nDownloading production backup for {$this->date}\n", Console::BOLD);
        try {
            system("ncftpget -u {$ftpConfig['username']} -p {$ftpConfig['password']} {$ftpConfig['host']} /app/sql/ /gigadbv3_{$this->date}.backup", $downloadStatus);
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }

        $this->stdout("\nRestoring the backup for {$this->date}\n", Console::BOLD);
        try {
            system("PGPASSWORD=$dbPassword dropdb -U $dbUser -h {$dbConfig['host']} --if-exists {$dbConfig['database']}");
            system("PGPASSWORD=$dbPassword createdb -U $dbUser -h {$dbConfig['host']} -T template0 {$dbConfig['database']}");
            system("PGPASSWORD=$dbPassword pg_restore --exit-on-error --verbose --use-list sql/pg_restore.list -h {$dbConfig['host']} -U $dbUser --dbname {$dbConfig['database']}  sql/gigadbv3_{$this->date}.backup");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return ExitCode::OK;
    }

    /**
     * This command will list the dataset with files in need of updating for ftp urls replacement
     *
     * Usage:
     * ./yii dataset-files/list-pending-datasets --all|--next <batch size> --after <dataset id>
     *
     * @return int Exit code
     */
    public function actionListPendingDatasets()
    {
        $rows = [];
        if ($this->all) {
            $rows = DatasetFiles::build()->getAllPendingDatasets();

            foreach ($rows as $key => $value) {
                $this->stdout($value["dataset_id"]."\n");
            }

            return ExitCode::OK;
        }
        elseif ($this->next) {
            $rows = DatasetFiles::build()->getNextPendingDatasets($this->after, $this->next);

            foreach ($rows as $key => $value) {
                $this->stdout($value["dataset_id"]."\n");
            }
            return ExitCode::OK;
        }

        return ExitCode::NOINPUT;

    }

    /**
     * This command will update file table to replace ftp urls for the supplied list of dataset ids
     *
     *  Usage:
     *      ./yii dataset-files/update-ftp-url --next <batch size> [--after <dataset id>][--dry-run]
     * TODO: to implement
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionUpdateFtpUrl()
    {
        $this->stdout("\nTransforming ftp urls...\n", Console::BOLD);
        try {
            system("true");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return ExitCode::OK;
    }

}
