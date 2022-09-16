<?php
namespace console\tests\functional;
use common\models\EMReportJob;
use common\models\Ingest;
use console\tests\FunctionalTester;
use yii\console\ExitCode;

class EMReportJobCest
{
    public function _before(FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test migrate/down all --interactive=0", false);
        $I->runShellCommand("./yii_test migrate/up --interactive=0", false);
    }

    public function _after(FunctionalTester $I)
    {
        if (file_exists("console/tests/_data/Report-GIGA-em-manuscripts-latest-214-20220611007777.csv"))
            unlink("console/tests/_data/Report-GIGA-em-manuscripts-latest-214-20220611007777.csv");
    }

    // tests
    public function tryToPushManuscriptQueueJobToManuscriptTableAndUpdateStatusInIngestTable(FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->canSeeInDatabase("ingest", ["file_name"=>"Report-GIGA-em-manuscripts-latest-214-20220607004243.csv", "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>null, "store_status"=>null, "remote_file_status"=>null]);
        $I->cantSeeInDatabase("ingest", ["file_name"=>"Report-GIGA-em-manuscripts-latest-214-20220607004243.csv", "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>Ingest::PARSE_STATUS_YES, "store_status"=>Ingest::STORE_STATUS_YES, "remote_file_status"=>Ingest::REMOTE_FILES_STATUS_EXISTS]);

        $I->runShellCommand("/usr/local/bin/php /app/yii_test manuscripts-q/run --verbose", false);
        $I->canSeeInDatabase("manuscript", ["manuscript_number" => "GIGA-D-22-00054", "article_title" => "A machine learning framework for discovery and enrichment of metagenomics metadata from open access publications", "editorial_status" => "Final Decision Accept", "editorial_status_date" => "2022-06-07"]);

        // Manuscript with invalid editorial status is not saved to manuscript table
        $I->cantSeeInDatabase("manuscript", ["manuscript_number" => "GIGA-D-22-00060", "article_title" => "A chromosome-level genome of the booklouse, Liposcelis brunnea provides insight into louse evolution and environmental stress adaptation", "editorial_status" => "Final Decision Reject", "editorial_status_date" => "2022-06-07"]);
        $I->cantSeeInDatabase("manuscript", ["manuscript_number" => "GIGA-D-22-00030", "article_title" => "A novel ground truth multispectral image dataset with weight, anthocyanins and brix index measures of grape berries tested for its utility in machine learning pipelines", "editorial_status" => "Final Decision Pending", "editorial_status_date" => "2022-06-07"]);

        // Manuscript with invalid manuscript number format is not saved to manuscript table
        $I->cantSeeInDatabase("manuscript", ["manuscript_number" => "GIGA-D-22-abcde", "article_title" => " Test manuscript review with invalid manuscript number format", "editorial_status"=> "Final Decision Accept","editorial_status_date" => "2022-06-07"]);

        // Manuscript with invalid date format is not saved to manuscript table
        $I->cantSeeInDatabase("manuscript", ["manuscript_number" => "GIGA-D-22-00099", "article_title" => " Test manuscript review with invalid date format", "editorial_status"=> "Final Decision Accept","editorial_status_date" => "2022-06-07"]);

        $I->canSeeInDatabase("ingest", ["file_name"=>"Report-GIGA-em-manuscripts-latest-214-20220607004243.csv", "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>null, "store_status"=>null, "remote_file_status"=>null]);
        $I->canSeeInDatabase("ingest", ["file_name"=>"Report-GIGA-em-manuscripts-latest-214-20220607004243.csv", "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>Ingest::PARSE_STATUS_YES, "store_status"=>Ingest::STORE_STATUS_YES, "remote_file_status"=>Ingest::REMOTE_FILES_STATUS_EXISTS]);

        $I->canSeeResultCodeIs(Exitcode::OK);
    }

    public function tryToMatchSampleManuscriptReportWithEntriesFoundInManuscriptTableAndUpdateStatusInIngestTable(FunctionalTester $I)
    {
        $csvReportDir = "console/tests/_data/";

        $sampleCsvReportName = "Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";

        $sampleCsvReportData = EMReportJob::parseReport($csvReportDir.$sampleCsvReportName);

        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->canSeeInDatabase("ingest", ["file_name"=>"Report-GIGA-em-manuscripts-latest-214-20220607004243.csv", "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>null, "store_status"=>null, "remote_file_status"=>null]);
        $I->cantSeeInDatabase("ingest", ["file_name"=>"Report-GIGA-em-manuscripts-latest-214-20220607004243.csv", "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>Ingest::PARSE_STATUS_YES, "store_status"=>Ingest::STORE_STATUS_YES, "remote_file_status"=>Ingest::REMOTE_FILES_STATUS_EXISTS]);

        $I->runShellCommand("/usr/local/bin/php /app/yii_test manuscripts-q/run --verbose", false);

        $I->canSeeInDatabase("manuscript", $sampleCsvReportData[0]);

        // Manuscript with invalid editorial status is not saved to manuscript table
        $I->cantSeeInDatabase("manuscript", $sampleCsvReportData[1]);
        $I->cantSeeInDatabase("manuscript", $sampleCsvReportData[2]);

        // Manuscript with invalid manuscript number format is not saved to manuscript table
        $I->cantSeeInDatabase("manuscript", $sampleCsvReportData[3]);

        // Manuscript with invalid date format is not saved to manuscript table
        $I->cantSeeInDatabase("manuscript", $sampleCsvReportData[4]);

        $I->canSeeInDatabase("ingest", ["file_name"=>$sampleCsvReportName, "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>null, "store_status"=>null, "remote_file_status"=>null]);
        $I->canSeeInDatabase("ingest", ["file_name"=>$sampleCsvReportName, "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>Ingest::PARSE_STATUS_YES, "store_status"=>Ingest::STORE_STATUS_YES, "remote_file_status"=>Ingest::REMOTE_FILES_STATUS_EXISTS]);

        $I->canSeeResultCodeIs(Exitcode::OK);
    }

    public function tryToSeeNoResultsManuscriptReportNotStoredToManuscriptTableAndUpdateStatusInIngestTable(FunctionalTester $I)
    {
        // Create temporary no result report with more recent timestamp console/tests/_data
        // so this file will be fetched, as it is the latest
        $noResultCsvReportDir = "console/tests/_data/";
        $tempNoResultCsvReportName = "Report-GIGA-em-manuscripts-latest-214-20220611007777.csv";
        file_put_contents($noResultCsvReportDir.$tempNoResultCsvReportName, "No Results");

        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->canSeeInDatabase("ingest", ["file_name"=>$tempNoResultCsvReportName, "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>null, "store_status"=>null, "remote_file_status"=>null]);
        $I->cantSeeInDatabase("ingest", ["file_name"=>$tempNoResultCsvReportName, "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>Ingest::PARSE_STATUS_YES, "store_status"=>Ingest::STORE_STATUS_YES, "remote_file_status"=>Ingest::REMOTE_FILES_STATUS_EXISTS]);

        $I->runShellCommand("/usr/local/bin/php /app/yii_test manuscripts-q/run --verbose", false);
        $I->canSeeInDatabase("ingest", ["file_name"=>$tempNoResultCsvReportName, "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>null, "store_status"=>null, "remote_file_status"=>null]);
        $I->canSeeInDatabase("ingest", ["file_name"=>$tempNoResultCsvReportName, "report_type"=>Ingest::REPORT_TYPES['manuscripts'], "fetch_status"=>Ingest::FETCH_STATUS_DISPATCHED, "parse_status"=>Ingest::PARSE_STATUS_NO, "store_status"=>Ingest::STORE_STATUS_NO, "remote_file_status"=>Ingest::REMOTE_FILES_STATUS_NO_RESULTS]);

        // To check the manuscript table is empty
        $I->seeNumRecords(0, "manuscript");
        $I->canSeeResultCodeIs(Exitcode::OK);
    }
}
