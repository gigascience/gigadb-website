<?php

use aik099\PHPUnit\BrowserTestCase;

class AdminFileTest extends BrowserTestCase {

	public static $browsers = array(
        array(
            'driver' => 'goutte',
            'browserName' => 'goutte',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

    public function setUp() {
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
     * The files form in the dataset wizard should display formatted file size
     *
     * @dataProvider adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     */
    public function testItShouldDisplayFormattedFileSize($size_value, $size_unit) {
    	// This is Mink's Session. We assumed we are already logged in as an Admin
        $session = $this->getSession();

        $url = "http://gigadb.dev/adminFile/create1/id/211/" ;
        $session->visit($url);

        echo $session->getCurrentUrl().PHP_EOL;
        // Validate text presence on a page.
        $this->assertTrue($session->getPage()->hasContent("$size_value $size_unit"));

    }

    /**
     * The form for updating files should display formatted file size
     *
     * @dataProvider adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     */
    public function testItShouldDisplayFormattedFileSizeOnUpdate($size_value, $size_unit) {
        // This is Mink's Session. We assumed we are already logged in as an Admin
        $session = $this->getSession();

        // Go to the file admin tab of the Dataset wizard
        $url = "http://gigadb.dev/adminFile/update1/?id=211" ;
        $session->visit($url);

        // Validate text presence on a page.
        $this->assertTrue($session->getPage()->hasContent("$size_value $size_unit"));

    }

    /**
     * Provide test data for functional testing of file size display
     *
     * Requires database to be loaded with gigadb_test_data.pgdmp
     * as those are real example from test dataset
     *
     * Expectations are for Metric (decimal) display of file size
     *
     * @see https://en.wikipedia.org/wiki/Gigabyte
     * @see https://packagist.org/packages/gabrielelana/byte-units
     *
     * @return array[][]
     **/
    public function adminFileExamplesOfAppropriateMetricDisplayOfFileSize() {
        return [
            'millet.chr.version2.3.fa.gz: 109B' => ["109", "B"],
            'Millet.fa.glean.cds.v3.gz: 13000B' => ["13.00", "kB"],
            'Millet.fa.glean.pep.v3.gz: 85000000B' => ["85.00", "MB"],
            'Millet.fa.glean.v3.gff: 14000000B' => ["14.00", "MB"],
            'Millet_scaffoldVersion2.3.fa.gz: 109000B' => ["109.00", "kB"],
        ];
    }


}

?>