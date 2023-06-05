<?php

namespace tests\functional;

use app\components\DatasetFilesUpdater;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;

class ReplaceFileUrlSubstringWithPrefixCest
{
    private const TEST_URLS = [
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/Diagram-ALL-FIELDS-Check-annotation.jpg",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/readme.txt",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/SRAmetadb.zip",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/"
    ];

    public function tryReplaceFileUrlSubstringWithPrefix(\FunctionalTester $I): void
    {
        $webClient = new Client([ 'allow_redirects' => false ]);
        $us = new URLsService();
        $dfu = new DatasetFilesUpdater([
            "doi" => '100142',
            "prefix" => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live',
            "separator" => '/pub/',
            "us" => $us,
            "webClient" => $webClient
        ]);
        $success = $dfu->replaceFileUrlSubstringWithPrefix();
    }

    public function tryGetPendingDatasets(\FunctionalTester $I): void
    {
        $webClient = new Client([ 'allow_redirects' => false ]);
        $us = new URLsService();
        $dfu = new DatasetFilesUpdater([
            "doi" => '100142',
            "prefix" => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live',
            "separator" => '/pub/',
            "us" => $us,
            "webClient" => $webClient
        ]);
        $dfu->getNextPendingDatasets('100142', 5);
    }
}
