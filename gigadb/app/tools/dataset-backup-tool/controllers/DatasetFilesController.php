<?php

namespace app\controllers;

use \yii\helpers\Console;
use \yii\console\Controller;
use \yii\console\ExitCode;

/**
 * The tool for uploading dataset files into a bucket
 * @package app\commands
 */
class DatasetFilesController extends Controller
{
    /**
     * @var bool true if dry run mode is activated, false otherwise (default)
     */
    public bool $dryrun = false;

    /**
     * @var bool true to show the full audit of transformation
     */
    public bool $verbose = false;

    /**
     * @var string the directory path where dataset files are located
     */
    public string $sourcedir = "tests/_data/dataset1/";

    /**
     * @var string the path to directory where dataset files will be uploaded to
     */
    public string $destdir = "dataset/";

    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help','config','date','next','after','dryrun','verbose','nodownload','latest'];
    }

    /**
     * This command will upload files to a Tencent COS bucket
     *
     * TODO: to implement
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionUploadFilesToBucket()
    {
        $optSourcedir = $this->sourcedir;
        $optDestdir = $this->destdir;

        $this->stdout("\nUploading files to bucket\n", Console::BOLD);
        try {
            $output = shell_exec("coscmd --debug --config_path scripts/.cos.conf upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rsy --delete ".$optSourcedir." ".$optDestdir." 2>&1");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        print_r($output);
        return ExitCode::OK;
    }

//    /**
//     * This command will create a Tencent COS bucket
//     *
//     * @throws \Throwable
//     * @return int Exit code
//     */
//    public function actionCreateBucket()
//    {
//        $this->stdout("\nCreating Bucket for {$this->date}\n", Console::BOLD);
//        try {
//            $output = shell_exec("scripts/create_bucket.sh");
//        }
//        catch (Throwable $e) {
//            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
//            Yii::error($e->getMessage());
//            return ExitCode::OSERR;
//        }
//        return $output;
//    }
//
//    /**
//     * This command will delete a Tencent COS bucket
//     *
//     * @throws \Throwable
//     * @return int Exit code
//     */
//    public function actionDeleteBucket()
//    {
//        $this->stdout("\nDeleting Bucket for {$this->date}\n", Console::BOLD);
//        try {
//            $output = shell_exec("scripts/delete_bucket.sh");
//        }
//        catch (Throwable $e) {
//            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
//            Yii::error($e->getMessage());
//            return ExitCode::OSERR;
//        }
//        return $output;
//    }
//
//    /**
//     * This command will delete directory
//     *
//     * @throws \Throwable
//     * @return int Exit code
//     */
//    public function actionDeleteDirectory($directory)
//    {
//        $this->stdout("\nDeleting directory for {$this->date}\n", Console::BOLD);
//
//        try {
//            // shell_exec returns complete output as string
//            $output = shell_exec("coscmd -c ./scripts/.cos.conf delete -r -f ".$directory." 2>&1");
//        }
//        catch (Throwable $e) {
//            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
//            Yii::error($e->getMessage());
//            return ExitCode::OSERR;
//        }
//        return $output;
//    }
//
//    /**
//     * This command will list the contents of a directory in a Tencent back up
//     *
//     * @throws \Throwable
//     * @return int Exit code
//     */
//    public function actionListContents($directory)
//    {
//        $this->stdout("\nListing contents for {$this->date}\n", Console::BOLD);
//
//        try {
//            // shell_exec returns complete output as string
//            $output = shell_exec("coscmd -c ./scripts/.cos.conf list ".$directory." 2>&1");
//        }
//        catch (Throwable $e) {
//            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
//            Yii::error($e->getMessage());
//            return ExitCode::OSERR;
//        }
//        return $output;
//    }
//
//    /**
//     * This command will backup a dataset directory to Tencent bucket
//     *
//     * @throws \Throwable
//     * @return int Exit code
//     */
//    public function actionBackupDataset()
//    {
//        $this->stdout("\nBacking up dataset for {$this->date}\n", Console::BOLD);
//        try {
//            $output = shell_exec("coscmd --debug --config_path scripts/.cos.conf upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rs tests/_data/dataset1/ dataset/ 2>&1");
//        }
//        catch (Throwable $e) {
//            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
//            Yii::error($e->getMessage());
//            return ExitCode::OSERR;
//        }
//        //        return ExitCode::OK;
//        return $output;
//    }
//
//    /**
//     * This command will backup dataset2 to Tencent bucket
//     *
//     * @throws \Throwable
//     * @return int Exit code
//     */
//    public function actionUpdateBackupWithChangedFile()
//    {
//        $this->stdout("\nUpdating backup with changed CSV for {$this->date}\n", Console::BOLD);
//        try {
//            $output = shell_exec("coscmd --debug --config_path scripts/.cos.conf upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rs tests/_data/dataset2/ dataset/ 2>&1");
//        }
//        catch (Throwable $e) {
//            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
//            Yii::error($e->getMessage());
//            return ExitCode::OSERR;
//        }
//        return $output;
//    }
//
//    /**
//     * This command will backup dataset2 to Tencent bucket 2>&1
//     *
//     * @throws \Throwable
//     * @return int Exit code
//     */
//    public function actionUpdateBackupWithDeletedFile()
//    {
//        $this->stdout("\nUpdating backup with deleted TSV file for {$this->date}\n", Console::BOLD);
//        try {
//            $output = shell_exec("coscmd --debug --config_path scripts/.cos.conf upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rsy --delete tests/_data/dataset3/ dataset/ 2>&1");
//        }
//        catch (Throwable $e) {
//            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
//            Yii::error($e->getMessage());
//            return ExitCode::OSERR;
//        }
//        return $output;
//    }
//
//    /**
//     * Display for information for a given file $filepath
//     *
//     * @throws \Throwable
//     * @return int Exit code
//     */
//    public function actionViewFileInfo($filepath)
//    {
//        $this->stdout("\nViewing file information for {$this->date}\n", Console::BOLD);
//        try {
//            $output = shell_exec("coscmd -c ./scripts/.cos.conf info ".$filepath." 2>&1");
//        }
//        catch (Throwable $e) {
//            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
//            Yii::error($e->getMessage());
//            return ExitCode::OSERR;
//        }
//        //return ExitCode::OK;
//        return $output;
//    }
}
