<?php

namespace tests\functional;

use app\components\DatasetFilesUpdater;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;

class UpdateFileSizeCest
{
    private const TEST_URLS = [
        "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100142/readme_100142.txt",
        "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100142",
        "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100142/",
    ];

    public function tryFetchFileSizeFromFilesUrl(\FunctionalTester $I): void
    {
        $expectedLengthList = [
            1523,
            0,
            0,
        ];

        $u = new URLsService(["urls" => self::TEST_URLS]);
        $I->assertTrue(is_a($u, "GigaDB\\services\\URLsService"));

        $zeroOutRedirectsAndDirectories = function ($response, $url) {
            if (403 === $response->getStatusCode() || str_ends_with($url, "/")) {
                return 0;
            }
            return null;
        };
        $webClient = new Client([ 'allow_redirects' => false ]);
        $contentLengthList = $u->fetchResponseHeader("Content-Length", $webClient, $zeroOutRedirectsAndDirectories);

        foreach ($expectedLengthList as $index => $expectedLength) {
            $I->assertEquals(
                $expectedLength,
                array_values($contentLengthList)[$index] ?? 0, // Use 0 if the index is not set in contentLengthList
                array_keys($contentLengthList)[$index] ?? "Directory has no content length"
            );
        }
    }

    public function tryUpdateFileSizeWhenContentLengthInBytes(\FunctionalTester $I): void
    {
        $webClient = new Client([ 'allow_redirects' => false ]);
        $us = new URLsService();
        $dfu = new DatasetFilesUpdater(["doi" => "100142", "us" => $us, "webClient" => $webClient]);
        $success = $dfu->updateFileSize();
        $I->assertEquals(1, $success, "Not all files were updated successfully");
    }
}
