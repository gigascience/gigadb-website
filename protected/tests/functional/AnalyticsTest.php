<?php

use aik099\PHPUnit\BrowserTestCase;

class AnalyticsTest extends BrowserTestCase
{

	public static $browsers = array(
        array(
            'driver' => 'goutte',
            'browserName' => 'goutte',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

    public function setUp()
    {
        // This is Mink's Session.
        $session = $this->getSession();

        //First login as an admin
        $session->visit("http://gigadb.dev/site/login");
        $session->getPage()->fillField("LoginForm_username", "admin@gigadb.org");
        $session->getPage()->fillField("LoginForm_password", "gigadb");
        $session->getPage()->pressButton("Login");

        $this->assertTrue($session->getPage()->hasContent("Admin"));
    }

    /**
     * ReportController::actionIndex loads analytics report page
     *
     * The test aims to test the credentials for connecting to Google API are configured correctly
     * Otherwise, a 500 Server Error will be returned. That's why we test the return code
     */
    public function testItShouldLoadGoogleAnalyticsPage()
    {
        // This is Mink's Session. We assumed we are already logged in as admin.
        $session = $this->getSession();

        // Make a call to the report controller
        $url = "http://gigadb.dev/report/index" ;
        $session->visit($url);

        // fill the form needed for the report
        $session->getPage()->fillField("Report_start_date", "2018-09-01");
        $session->getPage()->fillField("Report_end_date", "2018-09-30");
        $session->getPage()->selectFieldOption("Report_ids", "all");
        $session->getPage()->pressButton("View");

        // Check that after submission we land on the same view
        $this->assertEquals( "http://gigadb.dev/report/index", $session->getCurrentUrl() );

        // Check that the status code is 200 OK
        $this->assertEquals( 200,  $session->getStatusCode() );

    }

}

?>