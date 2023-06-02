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

    public array $excludedDois = [];

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
     * ./yii update/urls --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live --separator=pub --exclude-dois=[1000,234324,43534534]
     *
     * @return int
     */
    public function actionUrls(): int
    {
        $webClient = new Client([ 'allow_redirects' => false ]);
        $us = new URLsService();
        $dfu = new DatasetFilesUpdater([
            "doi" => $this->doi,
            "prefix" => $this->prefix,
            "separator" => $this->separator,
            "excludedDois" => $this->excludedDois,
            "us" => $us,
            "webClient" => $webClient
        ]);
        $success = $dfu->replaceFileUrlSubstringWithPrefix();
        $this->stdout("Number of changes: $success" . PHP_EOL, Console::FG_GREEN);
        return ExitCode::OK;
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'color', 'interactive', 'help', 'doi', 'prefix', 'separator', 'exclude-dois'
        ]);
    }
}
