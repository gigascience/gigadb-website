<?php
namespace console\tests;

use Behat\Gherkin\Node\TableNode;
use common\models\Ingest;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    /**
     * @Given EM reports are uploaded daily to a sftp server
     *
     * Note: only this step needs to talk to the real SFTP server, all subsequent steps will you test environment for speed and isolation
     */
    public function EMReportsAreUploadedDailyToASftpServer()
    {
//        $testDate= (new \DateTime("yesterday"))->format("Ymd");
        $testDate = "20220607";
        $reports = shell_exec("./yii_test fetch-reports/list -$testDate") ;
        codecept_debug($reports);
        $this->assertContains("Report-GIGA-em-manuscripts-latest",$reports);
        $this->assertContains("Report-GIGA-em-authors-latest",$reports);
        $this->assertContains("Report-GIGA-em-questions-latest",$reports);
        $this->assertContains("Report-GIGA-em-reviewers-latest",$reports);
        $this->assertContains("Report-GIGA-em-reviews-latest",$reports);
    }

    /**
     * @When EM report files are on the sftp server
     */
    public function EMReportFilesAreOnTheSftpServer()
    {
        $testDate="20220607";
        $reports = shell_exec("./yii_test fetch-reports/list -$testDate") ;
        $this->assertContains("Report-GIGA-em-authors-latest",$reports);
        $this->assertContains("Report-GIGA-em-manuscripts-latest",$reports);
        $this->assertContains("Report-GIGA-em-questions-latest",$reports);
        $this->assertContains("Report-GIGA-em-reviewers-latest",$reports);
        $this->assertContains("Report-GIGA-em-reviews-latest",$reports);
    }

    /**
     * @When the file ingester has run
     */
    public function theFileIngesterHasRun()
    {
        $testDate="20220610";
        $output = shell_exec("./yii_test fetch-reports/fetch -$testDate") ;
        codecept_debug($output);

    }


    /**
     * @Then the EM :report_type report spreadsheet is downloaded
     */
    public function theEMReportSpreadsheetIsDownloaded($report_type)
    {
        $this->seeInDatabase('ingest',[
            "file_name" =>"Report-GIGA-em-$report_type-latest-214-20220607004243.csv",
            "report_type" => Ingest::REPORT_TYPES[$report_type],
            "fetch_status" => Ingest::FETCH_STATUS_DISPATCHED,
            "parse_status" => null,
            "remote_file_status" => null,
            "store_status" => null,
        ]);
    }

    /**
     * @Then the EM :report_type no results report spreadsheet is downloaded
     */
    public function theEMNoResultsReportIsDownloaded($report_type)
    {
        $this->seeInDatabase('ingest',[
            "file_name" =>"Report-GIGA-em-$report_type-latest-214-20220611007777.csv",
            "report_type" => Ingest::REPORT_TYPES[$report_type],
            "fetch_status" => Ingest::FETCH_STATUS_DISPATCHED,
        ]);
    }

    /**
     * @When the :report_type worker executes the queue job
     */
    public function theWorkerExecutesTheQueueJob($report_type)
    {
        $output = shell_exec("/usr/local/bin/php /app/yii_test $report_type-q/run --verbose");
        codecept_debug($output);
    }

    /**
     * @Then the EM :report_type report spreadsheet is parsed and saved
     */
    public function theEMReportSpreadsheetIsParsedAndSaved($report_type)
    {
        $this->seeInDatabase('ingest',[
            "file_name" =>"Report-GIGA-em-$report_type-latest-214-20220607004243.csv",
            "report_type" => Ingest::REPORT_TYPES[$report_type],
            "parse_status" => Ingest::PARSE_STATUS_YES,
            "store_status" => Ingest::STORE_STATUS_YES,
            "remote_file_status" => Ingest::REMOTE_FILES_STATUS_EXISTS,
        ]);
    }

    /**
     * @Then the EM :report_type report spreadsheet is not parsed nor saved
     */
    public function theEMReportSpreadsheetIsNotParsedNorSaved($report_type)
    {
        $this->seeInDatabase('ingest',[
            "file_name" =>"Report-GIGA-em-$report_type-latest-214-20220611007777.csv",
            "report_type" => Ingest::REPORT_TYPES[$report_type],
            "parse_status" => Ingest::PARSE_STATUS_NO,
            "store_status" => Ingest::STORE_STATUS_NO,
            "remote_file_status" => Ingest::REMOTE_FILES_STATUS_NO_RESULTS,
        ]);
    }


    /**
     * @Then I should see in :report_type table
     */
    public function iShouldSeeInTable($report_type, TableNode $table)
    {
        foreach ($table as $row) {
            $this->canSeeInDatabase($report_type, $row);
        }
    }

    /**
     * @Then the database is reset
     */
    public function theDatabaseIsClean()
    {
        $output = shell_exec("./yii_test migrate/fresh --interactive=0");
        codecept_debug($output);
    }

    /**
     * @Given the :report_type no results report is created and found in the sftp
     */
    public function theNoResultsReportIsCreatedAndFoundInTheSftp($report_type)
    {
        $noResultCsvReportDir = "console/tests/_data/";
        $tempNoResultCsvReportName = "Report-GIGA-em-$report_type-latest-214-20220611007777.csv";
        file_put_contents($noResultCsvReportDir.$tempNoResultCsvReportName, "No Results");

        $reports = shell_exec("./yii_test fetch-reports/list -20220611");
        codecept_debug($reports);
        $this->assertContains("Report-GIGA-em-$report_type-latest", $reports);
    }

    /**
     * @Then I should see :report_type table is empty
     */
    public function iShouldSeeTableIsEmpty($report_type)
    {
        $this->seeNumRecords(0, $report_type);
    }

    /**
     * @Then remove temporary :report_type no results report spreadsheet
     */
    public function removeTemporaryNoResultsReportSpreadsheet($report_type)
    {
        unlink("console/tests/_data/Report-GIGA-em-$report_type-latest-214-20220611007777.csv");
    }

}
