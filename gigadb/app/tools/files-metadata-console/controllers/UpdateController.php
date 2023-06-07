<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\DatasetFilesUpdater;
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
    public string $doi = "";

    public string $prefix = "";

    public string $separator = "";

    /**
     * @var array list of DOIs which should not have their URLs updated
     */
    public array $excludedDois = [];

    /**
     * @var int $next get list of next $next pending datasets
     */
    public int $next = 0;

    /**
     * @var bool true if dry run mode is activated, false otherwise (default)
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
        $dfu = new DatasetFilesUpdater(["doi" => $this->doi, "us" => $us, "webClient" => $webClient]);
        $success = $dfu->updateFileSize();
        $this->stdout("nb. changes: $success" . PHP_EOL, Console::FG_GREEN);
        return ExitCode::OK;
    }

    /**
     * Console command for updating the URL for all files in a dataset
     *
     * docker-compose run --rm files-metadata-console  ./yii update/urls --doi=100142 --next <batch size> --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live  --separator=/pub/ --exclude-dois=[1000,234324,43534534]
     *
     * @return int
     * @throws \Throwable
     */
    public function actionUrls(): int
    {
        //Managing input
        $optDoi = $this->doi;
        $optPrefix = $this->prefix;
        $optSeparator = $this->separator;
        $optNext = $this->next;
        $optApply = $this->apply;
        $optExcludedDois = $this->excludedDois;

        //Return usage unless mandatory options are passed
        if (!($optDoi) || !($optPrefix) || !($optSeparator)) {
            $this->stdout("\nUsage:\n\t./yii update/urls --doi <dataset doi> --prefix <URL prefix> --separator <substring to separate current URL> [--next <batch size>][--exclude-dois <comma separated list of dois>][--dryrun][--verbose]\n\n");
            return ExitCode::USAGE;
        }

        try {
            $dfu = DatasetFilesUpdater::build($optApply);
            $dois = $dfu->getNextPendingDatasets($optNext, $optExcludedDois);

            foreach ($dois as $doi) {
                $success = $dfu->replaceFileUrlSubstringWithPrefix($doi, $optSeparator, $optPrefix);
                $this->stdout("Number of file changes: $success on dataset DOI $doi" . PHP_EOL, Console::FG_GREEN);
            }
        } catch (\Throwable $e) {
            $this->stdout(" ftp_site ERROR", Console::BG_RED);
//            $this->stdout(" location FAILURE (0/$nbFiles)", Console::BG_RED);
//            $this->stdout("\n** Rolling back transaction for dataset of id {$value["dataset_id"]}\n", Console::BG_RED);
//            $transaction->rollBack();
            throw $e; //we stop the whole run
        }
        return ExitCode::OK;
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'color', 'interactive', 'help', 'doi', 'prefix', 'separator', 'excluded-dois', 'next', 'apply'
        ]);
    }
}
