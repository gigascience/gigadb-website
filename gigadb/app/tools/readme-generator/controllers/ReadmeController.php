<?php

namespace app\controllers;

use app\components\DatasetService;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\helpers\Console;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * The tool for generating dataset readme files
 * @package app\commands
 */
class ReadmeController extends Controller
{
    /**
     * @var string $doi The DOI of the dataset that the readme file should be created for
     */
    public $doi = "";

    /**
     * @var string $outdir The output directory that the readme file should be saved in
     */
    public $outdir = "";

    // Character width of text in readme file
    const STRING_WIDTH = 80;

    /**
     * Specify options available to console command provided by this controller.
     * Returns a list of controller class's public properties
     *
     * @var string $actionID blah
     */
    public function options($actionID): array
    {
        return ['doi', 'outdir'];
    }

    /**
     * This command will auto-create a readme file for a dataset given a doi
     *
     *  Usage:
     *      ./yii readme/create --doi
     * @throws Throwable
     * @return int Exit code
     */
    public function actionCreate(): int
    {
        $optDoi = $this->doi;
        $optOutdir = $this->outdir;
        
        //Return usage unless mandatory options are passed
        if (!($optDoi)) {
            $this->stdout(
                "\nUsage:\n\t./yii readme/create --doi 100142 | --outdir /home/curators".PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            # $readme = Yii::$app->datasetService->getReadme($optDoi);
            $ds = new \app\components\DatasetService();
            $readme = $ds->getReadme($optDoi);
            echo $readme;
            // Save file if output directory exists
            if ($optOutdir != null && is_dir($optOutdir)) {
                $filename = "$optOutdir/readme_$optDoi.txt";
                file_put_contents($filename, $readme);
            } elseif ($optOutdir != null && !is_dir($optOutdir)) {
                throw new Exception("Cannot save readme file - Output directory does not exist or is not a directory");
            }
        } catch (Exception $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }
}
