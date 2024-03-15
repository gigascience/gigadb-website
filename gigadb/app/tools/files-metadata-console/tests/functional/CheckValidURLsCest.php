<?php

namespace tests\functional;

use app\components\DatasetFilesUpdater;
use app\components\FilesURLsFetcher;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;

class CheckValidURLsCest
{
    private const TEST_URLS = [
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006",
        "https://example.com/myfile.txt",
        "ftp://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/",
    ];

    public function tryReportIssues(\FunctionalTester $I): void {
        $expectedIssues = [
            "URL appears to be a directory listing",
            "Resource cannot be downloaded, remote endpoint crashed (5xx)",
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
        $component = new FilesURLsFetcher(["doi" => "100005", "webClient" => $testWebClient]);
        $report = $component->verifyURLs();
        $I->assertEmpty($report);

    }

}