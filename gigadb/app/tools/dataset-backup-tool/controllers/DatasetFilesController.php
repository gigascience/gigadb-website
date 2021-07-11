<?php

namespace app\controllers;

use DirectoryIterator;
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
    public bool $verbose = true;

    /**
     * @var string the directory path where dataset files are located
     */
    public string $sourcedir = "tests/_data/dataset4/";

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
        $optVerbose = $this->verbose;

        // Ask for confirmation to proceed
        $this->stdout("\nWarning! ", Console::FG_RED);
        switch($this->confirm("This command will upload files into bucket, are you sure you want to proceed?\n")) {
            case false:
                $this->stdout("Aborting.\n", Console::FG_BLUE);
                return ExitCode::NOPERM;
            default:
                $this->stdout("Executing command...\n", Console::FG_BLUE);
        }

        // Check dataset files are world readable permission
        // Loop thru dataset directories
        // Loop thru files in directory and check is_readable
        // output all files and directories except for '.' and '..'
        $this->stdout("\nChecking file permissions in dataset directories\n", Console::BOLD);
        foreach (new DirectoryIterator($optSourcedir) as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }
            elseif($fileInfo->isDir()) {
                echo "Directory: " . $fileInfo->getFilename() . "\n";;
            }
            else {
                echo $fileInfo->getFilename() . "\n";
                $octal_perms = substr(sprintf('%o', $fileInfo->getPerms()), -4);
                if ((int)$octal_perms < 0644) echo $octal_perms . "\n";
            }
        }

        $this->stdout("\nUploading files to bucket\n", Console::BOLD);
        try {
            $output = shell_exec("coscmd --debug --config_path scripts/.cos.conf upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rsy --delete ".$optSourcedir." ".$optDestdir." 2>&1");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        if ($optVerbose) {
            $this->stdout("\nOutput from coscmd:\n\n", Console::BOLD );
            print_r($output);
        }
        return ExitCode::OK;
    }
}
