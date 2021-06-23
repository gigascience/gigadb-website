<?php

namespace app\controllers;

use \yii\helpers\Console;
use \yii\console\Controller;
use \yii\console\ExitCode;

/**
 * The tool for updating production database's file table to replace ftp urls
 * @package app\commands
 */
class BackupSmokeTestController extends Controller
{
    /**
     * This command will create a Tencent COS bucket
     *
     * TODO: to implement
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionCreateBucket()
    {
        try {
            $output = shell_exec("scripts/create_bucket.sh");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return $output;
    }

    /**
     * This command will delete a Tencent COS bucket
     *
     * TODO: to implement
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionDeleteBucket()
    {
        try {
            $output = shell_exec("scripts/delete_bucket.sh");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return $output;
    }

    /**
     * This command will delete directory
     *
     * TODO: to implement
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionDeleteDirectory($directory)
    {
        try {
            // shell_exec returns complete output as string
            $output = shell_exec("coscmd -c ./scripts/.cos.conf delete -r -f ".$directory." 2>&1");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return $output;
    }

    /**
     * This command will list the contents of a directory in a Tencent back up
     *
     * TODO: to implement
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionListContents($directory)
    {
        try {
            // shell_exec returns complete output as string
            $output = shell_exec("coscmd -c ./scripts/.cos.conf list ".$directory." 2>&1");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return $output;
    }

    /**
     * This command will backup dataset2 to Tencent bucket
     *
     * TODO: to implement
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionUpdateBackupWithChangedFile()
    {
        try {
            $output = shell_exec("coscmd --debug --config_path scripts/.cos.conf upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rs tests/_data/dataset2/ dataset/ 2>&1");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return $output;
    }

    /**
     * This command will backup dataset2 to Tencent bucket 2>&1
     *
     * TODO: to implement
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionUpdateBackupWithDeletedFile()
    {
        try {
            $output = shell_exec("coscmd --debug --config_path scripts/.cos.conf upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rsy --delete tests/_data/dataset3/ dataset/ 2>&1");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return $output;
    }

    /**
     * Display for information for a given file $filepath
     *
     * TODO: to implement
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionViewFileInfo($filepath)
    {
        try {
            $output = shell_exec("coscmd -c ./scripts/.cos.conf info ".$filepath." 2>&1");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        //return ExitCode::OK;
        return $output;
    }
}
