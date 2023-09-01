<?php

namespace app\controllers;

use Throwable;
use Yii;
use yii\base\Exception;
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
        ];
    }

    /**
     * This command will auto-create a readme file for a dataset given a doi
     *
     *  Usage:
     *      ./yii readme/create --doi
     *
     * @throws Exception When output directory cannot be found.
     * @return integer Exit code
     */
    public function actionCreate(): int
    {
        $optDoi    = $this->doi;
        $optOutdir = $this->outdir;

        // Return usage unless mandatory options are passed.
        if ($optDoi === '') {
            $this->stdout(
                "\nUsage:\n\t./yii readme/create --doi 100142 | --outdir /home/curators".PHP_EOL
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
        } catch (Exception $e) {
            $this->stderr($e->getMessage().PHP_EOL, Console::FG_RED);
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;

    }


}
