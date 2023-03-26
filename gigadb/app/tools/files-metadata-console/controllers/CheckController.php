<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\DatasetFilesUpdater;
use app\components\FilesURLsFetcher;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Console commands for verifying files meta-data
 *
 */
final class CheckController extends Controller
{
    public string $doi = "";

    /**
     * Console command for verifying that files url resolve
     *
     * ./yii check/valid-urls --doi=100142
     *
     * @return int
     */
    public function actionValidUrls(): int
    {
        $webClient = new Client([ 'allow_redirects' => false ]);
        $c = new FilesURLsFetcher(["doi" => $this->doi, "webClient" => $webClient]);
        $report = $c->checkURLs();
        $this->stdout("| URL | Issue |" . PHP_EOL, Console::BOLD);
        foreach ($report as $url => $reason) {
            $this->stdout("| " . $url . " | ", Console::FG_GREEN);
            $this->stdout($reason, Console::FG_RED);
            $this->stdout(" |" . PHP_EOL, Console::FG_GREEN);
        }
        return ExitCode::OK;
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'color', 'interactive', 'help','doi'
        ]);
    }
}
