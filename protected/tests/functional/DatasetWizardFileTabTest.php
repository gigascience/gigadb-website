<?php

use aik099\PHPUnit\BrowserTestCase;

class DatasetWizardFileTabTest extends BrowserTestCase {

	public static $browsers = array(
        array(
            'driver' => 'selenium2',
            'host' => 'phantomjs',
            'port' => 8910,
            'browserName' => 'phantomjs',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

    public function testItShouldDisplayFormattedFileSize() {
    	// This is Mink's Session.
        $session = $this->getSession();

        //First login as an admin
        $session->visit("http://gigadb.dev/site/login");
        $session->getPage()->fillField("LoginForm_username", "admin@gigadb.org");
        $session->getPage()->fillField("LoginForm_password", "gigadb");
        $session->getPage()->pressButton("Login");

        $this->assertTrue($session->getPage()->hasContent("Admin"));

        // Go to the file admin tab of the Dataset wizard
        $url = "http://gigadb.dev/adminFile/create1/id/211/sort/name#" ;
        $session->visit($url);

        // Validate text presence on a page.
        $this->assertTrue($session->getPage()->hasContent('0.11 KB'));
        $this->assertTrue($session->getPage()->hasContent('12.7 KB'));
        $this->assertTrue($session->getPage()->hasContent('81.06 MB'));
        $this->assertTrue($session->getPage()->hasContent('106.45 KB'));
        $this->assertTrue($session->getPage()->hasContent('0 KB'));
    }

}

?>