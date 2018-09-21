<?php

use aik099\PHPUnit\BrowserTestCase;

class ApiTest extends BrowserTestCase {

	public static $browsers = array(
        array(
            'driver' => 'goutte',
            'browserName' => 'goutte',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

	public function testItShouldOutputDatasetOnly() {
		// This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/api/dataset/doi/100002?result=dataset" ;

        // Go to a page and getting xml content
        $session->visit($url);
        $xml = $session->getPage()->getContent();

        // Loading content into an XML structure
        $feed = new SimpleXMLElement($xml);

        // Validate text presence on a page.
        $this->assertEquals("Genomic data from Adelie penguin (Pygoscelis adeliae). ", $feed->dataset->title);
        $this->assertNull($feed->samples->sample);
        $this->assertNull($feed->files->file);

	}

    public function testItShouldOutputSamplesOnly() {
        // This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/api/dataset/doi/100002?result=sample" ;

        // Go to a page and getting xml content
        $session->visit($url);
        $xml = $session->getPage()->getContent();

        // Loading content into an XML structure
        $feed = new SimpleXMLElement($xml);

        // Validate text presence on a page.
        $this->assertEquals("Pygoscelis_adeliae", $feed->samples->sample[0]->name);
        $this->assertEquals("9238", $feed->samples->sample[0]->species->tax_id);
        $this->assertNull($feed->files->file);
        $this->assertNull($feed->dataset->title);
    }

    public function testItShouldOutputFilesOnly() {
        // This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/api/dataset/doi/100002?result=file" ;

        // Go to a page and getting xml content
        $session->visit($url);
        $xml = $session->getPage()->getContent();

        // Loading content into an XML structure
        $feed = new SimpleXMLElement($xml);

        // Validate text presence on a page.
        $this->assertEquals("Pygoscelis_adeliae.cds.gz", $feed->files->file[0]->name);
        $this->assertNull($feed->samples->sample);
        $this->assertNull($feed->dataset->title);
    }

    public function testItShouldOutputFullDataset() {
        // This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/api/dataset/doi/100002?result=all" ;

        // Go to a page and getting xml content
        $session->visit($url);
        $xml = $session->getPage()->getContent();

        // Loading content into an XML structure
        $feed = new SimpleXMLElement($xml);

        // Validate text presence on a page.
        $this->assertEquals("Genomic data from Adelie penguin (Pygoscelis adeliae). ", $feed->dataset->title);
        $this->assertEquals("9238", $feed->samples->sample[0]->species->tax_id);
        $this->assertEquals("Pygoscelis_adeliae.cds.gz", $feed->files->file[0]->name);
    }

    public function testItShouldOutputFullDatasetByDefault() {
        // This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/api/dataset/doi/100002" ;

        // Go to a page and getting xml content
        $session->visit($url);
        $xml = $session->getPage()->getContent();

        // Loading content into an XML structure
        $feed = new SimpleXMLElement($xml);

        // Validate text presence on a page.
        $this->assertEquals("Genomic data from Adelie penguin (Pygoscelis adeliae). ", $feed->dataset->title);
        $this->assertEquals("9238", $feed->samples->sample[0]->species->tax_id);
        $this->assertEquals("Pygoscelis_adeliae.cds.gz", $feed->files->file[0]->name);
    }

    public function testItShouldSearchWithKeywordAndOutputDatasetOnly() {
        // This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/api/search?keyword=description:Antartica" ;

        // Go to a page and getting xml content
        $session->visit($url);
        $xml = $session->getPage()->getContent();

        // Loading content into an XML structure
        $feed = new SimpleXMLElement($xml);

        // Validate text presence on a page.
        $this->assertEquals("Genomic data from Adelie penguin (Pygoscelis adeliae). ", $feed->gigadb_entry->dataset->title);
        $this->assertNull($feed->gigadb_entry->samples->sample);
        $this->assertNull($feed->gigadb_entry->files->file);
    }

    public function testItShouldSearchWithKeywordAndOutputFileOnly() {
        // This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/api/search?keyword=description:Antartica&result=file" ;

        // Go to a page and getting xml content
        $session->visit($url);
        $xml = $session->getPage()->getContent();

        // Loading content into an XML structure
        $feed = new SimpleXMLElement($xml);

        // Validate text presence on a page.
        $this->assertEquals("Pygoscelis_adeliae.cds.gz", $feed->gigadb_entry->files->file[0]->name);
        $this->assertNull($feed->gigadb_entry->samples->sample);
        $this->assertNull($feed->gigadb_entry->dataset->title);
    }

    public function testItShouldSearchWithKeywordAndOutputSampleOnly() {
        // This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/api/search?keyword=description:Antartica&result=sample" ;

        // Go to a page and getting xml content
        $session->visit($url);
        $xml = $session->getPage()->getContent();

        // Loading content into an XML structure
        $feed = new SimpleXMLElement($xml);

        // Validate text presence on a page.
        $this->assertEquals("Pygoscelis_adeliae", $feed->gigadb_entry->samples->sample[0]->name);
        $this->assertNull($feed->gigadb_entry->files->file);
        $this->assertNull($feed->gigadb_entry->dataset->title);
    }

}

?>