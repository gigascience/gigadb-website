<?php

namespace tests\functional;

use app\components\DatasetFilesUpdater;
use app\components\FilesURLsFetcher;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;

class CheckValidURLsCest
{
    private const TEST_URLS = [
        "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100006",
        "ftp://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100006/readme_100006.txt",
        "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100006/",
    ];

    public function tryReportIssues(\FunctionalTester $I): void {
        $expectedIssues = [
            "Resource cannot be downloaded, not found or forbidden (4xx)",
            "Wrong scheme (ftp://)",
            "URL appears to be a directory (/)",
        ];
        $testWebClient = new Client([ 'allow_redirects' => false ]);
        $u = new URLsService(["urls" => self::TEST_URLS]);
        $report = $u->checkURLs($testWebClient);
        foreach ($expectedIssues as $index => $expectedIssue) {
            $I->assertEquals(
                $expectedIssue,
                array_values($report)[$index],
                array_keys($report)[$index]
            );
        }
    }

    public function tryHandleWrongDOI(\FunctionalTester $I): void {
        $testWebClient = new Client([ 'allow_redirects' => false ]);
        try {
            $component = new FilesURLsFetcher(["doi" => "100000", "webClient" => $testWebClient]);
            $report = $component->verifyURLs();
        } catch (\yii\console\Exception $e) {
            $I->assertEquals("DOI does not exist",$e->getMessage());
        }
    }

    public function tryNoIssueToReport(\FunctionalTester $I): void {
        $testWebClient = new Client([ 'allow_redirects' => false ]);
        $component = new FilesURLsFetcher(["doi" => "100142", "webClient" => $testWebClient]);
        $report = $component->verifyURLs();
        $I->assertEmpty($report);

    }

}
