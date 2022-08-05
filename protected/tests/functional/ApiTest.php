<?php

 /**
 * Functional test for the API endpoint
 *
 * It tests all combination of parameters and the feed has only the relevant data
 *
 * @uses \BrowserPageSteps::getXMLWithSessionAndUrl()
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class ApiTest extends FunctionalTesting
{
    use BrowserPageSteps;

	public function testItShouldOutputDatasetOnly() {
        $url = "http://gigadb.dev/api/dataset/doi/100006?result=dataset" ;

        // Go to a page and getting xml content
        $feed = $this->getXMLWithSessionAndUrl($url);

        // Validate text presence on a page.
        $this->assertEquals("Genomic data from Adelie penguin (Pygoscelis adeliae). ", $feed->dataset->title);
        $this->assertNull($feed->samples->sample);
        $this->assertNull($feed->files->file);

	}

    public function testItShouldOutputSamplesOnly() {
        $url = "http://gigadb.dev/api/dataset/doi/100006?result=sample" ;

        // Go to a page and getting xml content
        $feed = $this->getXMLWithSessionAndUrl($url);

        // Validate text presence on a page.
        $this->assertEquals("Pygoscelis_adeliae", $feed->samples->sample[0]->name);
        $this->assertEquals("9238", $feed->samples->sample[0]->species->tax_id);
        $this->assertNull($feed->files->file);
        $this->assertNull($feed->dataset->title);
    }

    public function testItShouldOutputFilesOnly() {
        $url = "http://gigadb.dev/api/dataset/doi/100006?result=file" ;

        // Go to a page and getting xml content
        $feed = $this->getXMLWithSessionAndUrl($url);

        // Validate text presence on a page.
        $this->assertEquals("Pygoscelis_adeliae.cds.gz", $feed->files->file[5]->name);
        $this->assertNull($feed->samples->sample);
        $this->assertNull($feed->dataset->title);
    }

    public function testItShouldOutputFullDataset() {
        $url = "http://gigadb.dev/api/dataset/doi/100006?result=all" ;

        // Go to a page and getting xml content
        $feed = $this->getXMLWithSessionAndUrl($url);

        // Validate text presence on a page.
        $this->assertEquals("Genomic data from Adelie penguin (Pygoscelis adeliae). ", $feed->dataset->title);
        $this->assertEquals("9238", $feed->samples->sample[0]->species->tax_id);
        $this->assertEquals("Pygoscelis_adeliae.cds.gz", $feed->files->file[5]->name);
    }

    public function testItShouldOutputFullDatasetByDefault() {
        $url = "http://gigadb.dev/api/dataset/doi/100006" ;

        // Go to a page and getting xml content
        $feed = $this->getXMLWithSessionAndUrl($url);

        // Validate text presence on a page.
        $this->assertEquals("Genomic data from Adelie penguin (Pygoscelis adeliae). ", $feed->dataset->title);
        $this->assertEquals("9238", $feed->samples->sample[0]->species->tax_id);
        $this->assertEquals("Pygoscelis_adeliae.cds.gz", $feed->files->file[5]->name);
    }

    public function testItShouldSearchWithKeywordAndOutputDatasetOnly() {
        $url = "http://gigadb.dev/api/search?keyword=description:Antartica" ;

        // Go to a page and getting xml content
        $feed = $this->getXMLWithSessionAndUrl($url);

        // Validate text presence on a page.
        $this->assertEquals("Genomic data from Adelie penguin (Pygoscelis adeliae). ", $feed->gigadb_entry->dataset->title);
        $this->assertNull($feed->gigadb_entry->samples->sample);
        $this->assertNull($feed->gigadb_entry->files->file);
    }

    public function testItShouldSearchWithKeywordAndOutputFileOnly() {
        $url = "http://gigadb.dev/api/search?keyword=description:Antartica&result=file" ;

        // Go to a page and getting xml content
        $feed = $this->getXMLWithSessionAndUrl($url);

        // Validate text presence on a page.
        $this->assertEquals("Pygoscelis_adeliae.cds.gz", $feed->gigadb_entry->files->file[0]->name);
        $this->assertNull($feed->gigadb_entry->samples->sample);
        $this->assertNull($feed->gigadb_entry->dataset->title);
    }

    public function testItShouldSearchWithKeywordAndOutputSampleOnly() {
        $url = "http://gigadb.dev/api/search?keyword=description:Antartica&result=sample" ;

        // Go to a page and getting xml content
        $feed = $this->getXMLWithSessionAndUrl($url);

        // Validate text presence on a page.
        $this->assertEquals("Pygoscelis_adeliae", $feed->gigadb_entry->samples->sample[0]->name);
        $this->assertNull($feed->gigadb_entry->files->file);
        $this->assertNull($feed->gigadb_entry->dataset->title);
    }

}

?>