<?php

use aik099\PHPUnit\BrowserTestCase;

class DatasetKeywordsTest extends BrowserTestCase
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
     * DatasetController::actionUpdate update keywords in the database
     *
     * @dataProvider keywordsProvider
     */
    public function testItShouldUpdateKeywords($input, $expectation)
    {
    	// This is Mink's Session. We assumed we are already logged in.
        $session = $this->getSession();

        // Make a call to the dataset update form
        $url = "http://gigadb.dev/dataset/update/id/210" ;
        $session->visit($url);

        // Add keywords and submit the form
        $session->getPage()->fillField("keywords", $input);
        $session->getPage()->pressButton("Save");

        // Check that after submission we land on the dataset view
        $this->assertEquals( "http://gigadb.dev/dataset/100002", $session->getCurrentUrl() );

        //Check that the content of the page match our $expectation given $input
        foreach($expectation as $keyword) {
	        $this->assertNotNull( $session->getPage()->findLink($keyword), "$keyword" );
        }

    }

    public function keywordsProvider()
    {
    	return [
    		"no keyword" => ["", []],
    		"two clean keywords" => ["bam, dam", ["bam", "dam"]],
    		"two keywords and one dodgy" => [" am",
    										 ["am"]],
    	];
    }

}
?>