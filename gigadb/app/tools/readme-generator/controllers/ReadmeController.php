<?php

namespace app\controllers;

use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\UserException;
use yii\helpers\Console;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * The tool for generating dataset readme files
 */
class ReadmeController extends Controller
{

    /**
     * DOI of the dataset that the readme file should be created for
     *
     * @var string $doi
     */
    public $doi = '';

    /**
     * The output directory that the readme file should be saved in
     *
     * @var string $outdir
     */
    public $outdir = '';

    /**
     * The wasabi bucket path that the readme file copied to
     *
     * @var string $bucketPath
     */
    public $bucketPath = '';

    /**
     * Specify options available to console command provided by this controller.
     *
     * @var    $actionID
     * @return array List of controller class's public properties
     */
    public function options($actionID): array
    {
        return [
            'doi',
            'outdir',
            'bucketPath',
        ];
    }

    /**
     * This command will auto-create a readme file for a dataset given a doi
     *
     *  Usage:
     *      ./yii readme/create --doi --outdir  --bucketPath
     *
     * @throws Exception When output directory cannot be found.
     * @return integer Exit code
     */
    public function actionCreate(): int
    {
        $optDoi    = $this->doi;
        $optOutdir = $this->outdir;
        $optBucketPath = $this->bucketPath;

        // Return usage unless mandatory options are passed.
        if ($optDoi === '' || $optOutdir === '' || $optBucketPath === '') {
            $this->stdout(
                "\nUsage:\n\t./yii readme/create --doi <DOI> --outdir <directory path> --bucketPath <bucket path>
                \nRequired Options:
                --doi         DOI to process, eg. 100142
                --outdir      Specify the output directory, eg. /home/curators
                --bucketPath  Path to the bucket, eg. wasabi:gigadb-datasets/dev/pub/10.5524".PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            $readme = Yii::$app->ReadmeGenerator->getReadme($optDoi);
            echo $readme;
            // Save file if output directory exists.
            if ($optOutdir !== '' && is_dir($optOutdir) === true) {
                $filename = $optOutdir.'/readme_'.$optDoi.'.txt';
                file_put_contents($filename, $readme);
            } else if ($optOutdir !== '' && is_dir($optOutdir) === false) {
                throw new Exception('Cannot save readme file - Output directory does not exist or is not a directory');
            }
            $md5 = md5_file($filename);
            $fileSize = filesize($filename);
            Yii::$app->ReadmeGenerator->updateOrCreate($optDoi, str_replace("$optOutdir/", "", $filename), $fileSize, $md5, $optBucketPath);
        } catch (UserException $e) {
            $this->stderr($e->getMessage().PHP_EOL, Console::FG_YELLOW);
            return ExitCode::DATAERR;
        } catch (Exception $e) {
            $this->stderr($e->getMessage().PHP_EOL, Console::FG_RED);
            return ExitCode::IOERR;
        }
        return ExitCode::OK;

    }


}
