<?php
namespace console\tests\functional;
use common\models\EMReportJob;
use console\tests\FunctionalTester;
use yii\console\ExitCode;

class EMReportJobCest
{
    public function _before(FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test migrate/down all --interactive=0", false);
        $I->runShellCommand("./yii_test migrate/up --interactive=0", false);
    }

    // tests
    public function tryToPushManuscriptQueueJobToTable(FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->runShellCommand("/usr/local/bin/php /app/yii_test manuscripts-q/run --verbose", false);
        $I->canSeeInDatabase('manuscript', ["manuscript_number" => "GIGA-D-22-00054", "article_title" => "A machine learning framework for discovery and enrichment of metagenomics metadata from open access publications", "editorial_status" => "Final Decision Accept", "editorial_status_date" => "2022-06-07"]);
        $I->canSeeInDatabase('manuscript', ["manuscript_number" => "GIGA-D-22-00060", "article_title" => "A chromosome-level genome of the booklouse, Liposcelis brunnea provides insight into louse evolution and environmental stress adaptation", "editorial_status" => "Final Decision Reject", "editorial_status_date" => "2022-06-07"]);
        $I->canSeeInDatabase('manuscript', ["manuscript_number" => "GIGA-D-22-00030", "article_title" => "A novel ground truth multispectral image dataset with weight, anthocyanins and brix index measures of grape berries tested for its utility in machine learning pipelines", "editorial_status" => "Final Decision Pending", "editorial_status_date" => "2022-06-07"]);
        $I->canSeeResultCodeIs(Exitcode::OK);
    }

    public function tryToMatchManuscriptReportWithTable(FunctionalTester $I)
    {
        $sampleCsvReport = "console/tests/_data/Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";

        $sampleCsvReportData = EMReportJob::parseReport($sampleCsvReport);

        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->runShellCommand("/usr/local/bin/php /app/yii_test manuscripts-q/run --verbose", false);

        foreach ($sampleCsvReportData as $row) {
            $I->canSeeInDatabase('manuscript', $row);
        }

        $I->canSeeResultCodeIs(Exitcode::OK);
    }

    public function tryToSeeNoResultsReportNotSaveToTable(FunctionalTester $I)
    {
        // Create temporary no result report with more recent timestamp console/tests/_data
        // so this file will be fetched, as it is the latest
        $noResultCsvReportDir = "console/tests/_data/";
        $tempNoResultCsvReportName = "Report-GIGA-em-manuscripts-latest-214-20220611007777.csv";
        file_put_contents($noResultCsvReportDir.$tempNoResultCsvReportName, "No Results");

        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->runShellCommand("/usr/local/bin/php /app/yii_test manuscripts-q/run --verbose", false);

        unlink($noResultCsvReportDir.$tempNoResultCsvReportName);

        // To check the manuscript table is empty
        $I->seeNumRecords(0, 'manuscript');
        $I->canSeeResultCodeIs(Exitcode::OK);
    }
}
