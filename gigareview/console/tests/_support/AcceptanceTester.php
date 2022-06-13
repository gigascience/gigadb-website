<?php
namespace console\tests;

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
     */
    public function aEMReportIsUploadedDailyToASftpServer()
    {
        codecept_debug("**** End of Given a EM report is uploaded daily to a sftp server ****");
    }

    /**
     * @When the file is on the sftp server
     */
    public function theFileIsOnTheSftpServer()
    {
        $testDate="20220610";
        $reports = shell_exec("./yii_test fetch-reports/list -$testDate") ;
        $this->assertContains("Report-GIGA-em-authors-latest",$reports);
        $this->assertContains("Report-GIGA-em-manuscripts-latest",$reports);
        $this->assertContains("Report-GIGA-em-questions-latest",$reports);
        $this->assertContains("Report-GIGA-em-reviewers-latest",$reports);
        $this->assertContains("Report-GIGA-em-reviews-latest",$reports);
        codecept_debug("**** End of When the file is on the sftp server ****");
    }

    /**
     * @When the file ingester has run
     */
    public function theFileIngesterHasRun()
    {
        $testDate="20220610";
        shell_exec("./yii_test fetch-reports/fetch -$testDate") ;
        codecept_debug("**** End of When the ingester has run ****");
    }


    /**
     * @Then the EM report spreadsheet is downloaded
     */
    public function theEMReportSpreadsheetIsDownloaded()
    {
        codecept_debug("**** End of Then the EM report spreadsheet is downloaded ****");
    }

}
