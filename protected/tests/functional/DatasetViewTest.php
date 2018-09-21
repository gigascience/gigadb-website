<?php

use aik099\PHPUnit\BrowserTestCase;

class DatasetViewTest extends BrowserTestCase {

	public static $browsers = array(
        array(
            'driver' => 'goutte',
            'browserName' => 'goutte',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

    /**
     * The Files tab on a dataset view should display formatted file size
     *
     * @uses AdminFileTest::adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     * @dataProvider AdminFileTest::adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     */
    public function testItShouldDisplayFormattedFileSize($size_value, $size_unit) {
    	// This is Mink's Session. We assumed we are already logged in as an Admin
        $session = $this->getSession();

        // Go to the file admin tab of the Dataset wizard
        $url = "http://gigadb.dev/dataset/view/id/100003/Files_page/1" ;
        $session->visit($url);

        // Validate text presence on a page.
        $this->assertTrue($session->getPage()->hasContent("$size_value $size_unit"));

    }

}

?>