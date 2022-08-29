<?php
namespace console\tests;

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
     * @Given a EM report is uploaded daily to a sftp server
     *
     * Note: only this step needs to talk to the real SFTP server, all subsequent steps will you test environment for speed and isolation
     */
    public function aEMReportIsUploadedDailyToASftpServer()
    {
        $testDate= (new \DateTime("yesterday"))->format("Ymd");
        $reports = shell_exec("./yii fetch-reports/list -$testDate") ;
        codecept_debug($reports);
        $this->assertContains("Report-GIGA-em-manuscripts-latest",$reports);
        $this->assertContains("Report-GIGA-em-authors-latest",$reports);
        $this->assertContains("Report-GIGA-em-questions-latest",$reports);
        $this->assertContains("Report-GIGA-em-reviewers-latest",$reports);
        $this->assertContains("Report-GIGA-em-reviews-latest",$reports);
    }

    /**
     * @When the file is on the sftp server
     */
    public function theFileIsOnTheSftpServer()
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

}
