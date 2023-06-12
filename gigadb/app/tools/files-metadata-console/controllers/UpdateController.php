<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\DatasetFilesURLUpdater;
use Exception;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;
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
     * @var string Starting dataset DOI to begin updating URLs
     */
    public string $doi = "";

    /**
     * @var string A new URL domain and path to where datasets have been re-located to 
     */
    public string $prefix = "";

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
     * Console command for updating files' size for the given dataset
     *
     * ./yii update/file-size --doi=100142
     *
     * @return int
     */
    public function actionFileSize(): int
    {
        $webClient = new Client([ 'allow_redirects' => false ]);
        $us = new URLsService();
        $dfu = new DatasetFilesURLUpdater(["doi" => $this->doi, "us" => $us, "webClient" => $webClient]);
        $success = $dfu->updateFileSize();
        $this->stdout("nb. changes: $success" . PHP_EOL, Console::FG_GREEN);
        return ExitCode::OK;
    }

    /**
     * Console command for updating the URL for all files in a dataset
     *
     * docker-compose run --rm files-metadata-console  ./yii update/urls --doi=100142 --next=10 --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live  --separator=/pub/ --exclude='1000465,1000665,1000765'
     *
     * @return int
     * @throws \Throwable
     */
    public function actionUrls(): int
    {
        // Manage inputs
        $optDoi = $this->doi;
        $optPrefix = $this->prefix;
        $optSeparator = $this->separator;
        $optNext = $this->next;
        $optApply = $this->apply;
        $optExcludedDois = $this->exclude;

        //Return usage unless mandatory options are passed
        if (!($optDoi) || !($optPrefix) || !($optSeparator)) {
            $this->stdout("\nUsage:\n\t./yii update/urls --doi <dataset doi> --prefix <URL prefix> --separator <substring to separate current URL> [--next <batch size>][--exclude-dois <comma separated list of dois>][--apply][--verbose]\n\n");
            return ExitCode::USAGE;
        }

        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $dfuu = DatasetFilesURLUpdater::build($optApply);
            $optExcludedDois = explode(',', $optExcludedDois);
            $dois = $dfuu->getNextPendingDatasets($optNext, $optExcludedDois);

            foreach ($dois as $doi) {
                $nbFiles = count($dfuu->queryFilesForDataset($doi));
                $this->stdout("\tTransforming ftp_site for dataset $doi... " . PHP_EOL);
                $ftpSiteOutcome = $dfuu->replaceFTPSiteForDataset($doi);
                if ($ftpSiteOutcome) {
                    $this->stdout("DONE" . PHP_EOL, Console::BG_GREEN);
                }
                else {
                    $this->stdout("ERROR" . PHP_EOL, Console::BG_RED);
                }

                $this->stdout("\n\tTransforming file locations for dataset $doi..." . PHP_EOL, Console::BOLD);
                $locationOutcome = $dfuu->replaceFileLocationsForDataset($doi, $optSeparator);
                switch ($locationOutcome) {
                    case 0:
                        $this->stdout("FAILURE (0/$nbFiles)" . PHP_EOL, Console::BG_RED);
                        break;
                    case $nbFiles:
                        $this->stdout("DONE ($nbFiles/$nbFiles)" . PHP_EOL, Console::BG_GREEN);
                        break;
                    default:
                        $this->stdout("ERROR ($locationOutcome/$nbFiles)" . PHP_EOL, Console::BG_YELLOW);
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
            'color', 'interactive', 'help', 'doi', 'prefix', 'separator', 'exclude', 'next', 'apply'
        ]);
    }
}
