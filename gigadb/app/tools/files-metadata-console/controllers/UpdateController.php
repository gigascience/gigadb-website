<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\DatasetFilesUpdater;
use app\components\DatasetFilesURLUpdater;
use Exception;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Console commands for manipulating files meta-data
 *
 */
final class UpdateController extends Controller
{
    /**
     * @var string DOI of dataset
     */
    public string $doi = "";

    /**
     * @var string Part of the URL which is used to split a URL in half
     */
    public string $separator = "";

    /**
     * @var string List of DOIs which should not have their URLs updated, e.g. '100020,100039'
     */
    public string $exclude = "";

    /**
     * @var int Number of datasets to be batch processed
     */
    public int $next = 0;

    /**
     * @var bool Include --apply flag to deactivate dry run mode
     */
    public bool $apply = false;

    /**
     * @var string A DOI , e.g. 200002 when it is reached that will stop this tool from processing any more datasets
     */
    public string $stop = '0';

    /**
     * Console command for updating size for all files in a given dataset
     *
     * ./yii update/file-sizes --doi=100142
     *
     * @return int
     */
    public function actionFileSizes(): int
    {
        try {
            $dfu = new DatasetFilesUpdater(["doi" => $this->doi]);
            $success = $dfu->updateFileSizes();
            $this->stdout("Number of changes: $success" . PHP_EOL);
        } catch (Exception $e) {
            $this->stdout($e->getMessage(), Console::FG_RED);
        }
        return ExitCode::OK;
    }

    /**
     * Console command for updating the URL for all files in a dataset
     *
     * docker-compose run --rm files-metadata-console ./yii update/urls --next=10 --separator=/pub/ --exclude='100046,100066,100076' --stop=200002
     *
     * @return int
     * @throws \Throwable
     */
    public function actionUrls(): int
    {
        // Manage inputs
        $optSeparator = $this->separator;
        $optNext = $this->next;
        $optApply = $this->apply;
        $optExcludedDois = $this->exclude;
        $optStopDoi = $this->stop;

        //Return usage unless mandatory options are passed
        if (!($optSeparator)) {
            $this->stdout("\nUsage:\n\t./yii update/urls --separator <substring to separate current URL> [--next <batch size>][--exclude <comma separated list of dois>][--stop <DOI to stop processing>][--apply][--verbose]\n\n");
            return ExitCode::USAGE;
        }

        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $dfuu = DatasetFilesURLUpdater::build($optApply);
            if (!$optApply) {
                $this->stdout("Running tool in dry run mode" . PHP_EOL, Console::FG_YELLOW);
            }

            $optExcludedDois = explode(',', $optExcludedDois);
            $dois = $dfuu->getNextPendingDatasets($optNext, $optExcludedDois);
            if (count($dois) == 0) {
                $this->stdout("No more datasets to process." . PHP_EOL, Console::FG_YELLOW);
            }

            foreach ($dois as $doi) {
                if ($optStopDoi != 0 && $doi >= $optStopDoi) {
                    $this->stdout("Stop DOI $optStopDoi been reached - processing will stop now." . PHP_EOL, Console::FG_YELLOW);
                    break;
                }
                $nbFiles = count($dfuu->queryFilesForDataset($doi));
                $this->stdout("\tTransforming ftp_site for dataset $doi... " . PHP_EOL);
                $ftpSiteOutcome = $dfuu->replaceFTPSiteForDataset($doi);
                if ($ftpSiteOutcome) {
                    $this->stdout("DONE" . PHP_EOL, Console::FG_GREEN);
                } else {
                    $this->stdout("ERROR" . PHP_EOL, Console::FG_RED);
                }

                $this->stdout("\n\tTransforming file locations for dataset $doi..." . PHP_EOL);
                $locationOutcome = $dfuu->replaceFileLocationsForDataset($doi, $optSeparator);
                switch ($locationOutcome) {
                    case 0:
                        $this->stdout("FAILURE (0/$nbFiles)" . PHP_EOL, Console::FG_RED);
                        break;
                    case $nbFiles:
                        $this->stdout("DONE ($nbFiles/$nbFiles)" . PHP_EOL, Console::FG_GREEN);
                        break;
                    default:
                        $this->stdout("WARNING ($locationOutcome/$nbFiles)" . PHP_EOL, Console::FG_YELLOW);
                        break;
                }
                $this->stdout("\n");
            }
            $transaction->commit();
        } catch (Exception $e) {
            $this->stdout($e, Console::BG_RED);
            $this->stdout("\n** Rolling back transaction\n", Console::BG_RED);
            $transaction->rollBack();
            throw $e; // Stop whole run
        }
        return ExitCode::OK;
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'color', 'interactive', 'help', 'doi', 'separator', 'exclude', 'next', 'apply', 'stop'
        ]);
    }
}
