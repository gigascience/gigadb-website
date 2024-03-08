<?php

/**
 * Unit tests for FormattedDatasetSamples to present the files associated to a dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetSamplesTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testFormattedReturnsDatasetId()
    {
        $dataset_id = 6;
        $pageSize = 10 ;
        // create a mock for the CachedDatasetSamples
        $cachedDatasetSamples = $this->getMockBuilder(CachedDatasetSamples::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetSamples->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(6);


        $daoUnderTest = new FormattedDatasetSamples($pageSize, $cachedDatasetSamples);
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testFormattedReturnsDatasetDOI()
    {
        $dataset_id = 6;
        $pageSize = 10 ;
        $doi = "100044";
         // create a mock for the CachedDatasetSamples
        $cachedDatasetSamples = $this->getMockBuilder(CachedDatasetSamples::class)
                         ->setMethods(['getDatasetDOI'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetSamples->expects($this->once())
                 ->method('getDatasetDOI')
                 ->willReturn("100044");


        $daoUnderTest = new FormattedDatasetSamples($pageSize, $cachedDatasetSamples);
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    /**
     * test that we get dataset samples whose attribute are suited for presentation (especially size and name)
     *
     */
    public function testFormattedReturnsDatasetSamples()
    {
        $dataset_id = 1;
        $pageSize = 2;

        $source = array(
            array(
                'sample_id' => 1,
                'linkName' => 'Sample 1',
                'dataset_id' => 1,
                'species_id' => 1,
                'tax_id' => 9238,
                'common_name' => 'Adelie penguin',
                'scientific_name' => 'Pygoscelis adeliae',
                'genbank_name' => 'Adelie penguin',
                'name' => "Sample 1",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'sample_attributes' => array(
                    array("keyword" => "some value"),
                    array("number of lines" => "155"),
                ),
            ),
            array(
                'sample_id' => 2,
                'linkName' => 'Sample 2',
                'dataset_id' => 1,
                'species_id' => 2,
                'tax_id' => 4555,
                'common_name' => 'Foxtail millet',
                'scientific_name' => 'Setaria italica',
                'genbank_name' => 'Foxtail millet',
                'name' => "Sample 2",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'sample_attributes' => [],
            ),
            array(
                'sample_id' => 3,
                'linkName' => 'Sample 3',
                'dataset_id' => 1,
                'species_id' => 1,
                'tax_id' => 9238,
                'common_name' => 'Adelie penguin',
                'scientific_name' => 'Pygoscelis adeliae',
                'genbank_name' => 'Adelie penguin',
                'name' => "Sample 3",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'sample_attributes' => [],
            ),
        );

        $expected = array(
            array(
                'sample_id' => 1,
                'linkName' => 'Sample 1',
                'dataset_id' => 1,
                'species_id' => 1,
                'tax_id' => 9238,
                'common_name' => 'Adelie penguin',
                'scientific_name' => 'Pygoscelis adeliae',
                'genbank_name' => 'Adelie penguin',
                'name' => "Sample 1",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'taxonomy_link' => '<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=9238">9238</a>',
                'sample_attributes' => array(
                    array("keyword" => "some value"),
                    array("number of lines" => "155"),
                ),
                'displayAttr' => "<span class=\"js-long-1\">Keyword:some value<br/>Number of lines:155<br/></span>",
            ),
            array(
                'sample_id' => 2,
                'linkName' => 'Sample 2',
                'dataset_id' => 1,
                'species_id' => 2,
                'tax_id' => 4555,
                'common_name' => 'Foxtail millet',
                'scientific_name' => 'Setaria italica',
                'genbank_name' => 'Foxtail millet',
                'name' => "Sample 2",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'taxonomy_link' => '<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=4555">4555</a>',
                'sample_attributes' => [],
                'displayAttr' => "",
            ),
            array(
                'sample_id' => 3,
                'linkName' => 'Sample 3',
                'dataset_id' => 1,
                'species_id' => 1,
                'tax_id' => 9238,
                'common_name' => 'Adelie penguin',
                'scientific_name' => 'Pygoscelis adeliae',
                'genbank_name' => 'Adelie penguin',
                'name' => "Sample 3",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'taxonomy_link' => '<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=9238">9238</a>',
                'sample_attributes' => [],
                'displayAttr' => "",
            ),
        );

        // create a mock for the CachedDatasetSamples
        $cachedDatasetSamples = $this->getMockBuilder(CachedDatasetSamples::class)
                         ->setMethods(['getDatasetSamples'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetSamples->expects($this->exactly(1))
                 ->method('getDatasetSamples')
                 ->willReturn($source);


        $daoUnderTest = new FormattedDatasetSamples($pageSize, $cachedDatasetSamples);
        $this->assertEquals($expected, $daoUnderTest->getDatasetSamples()) ;
    }

    /**
     * test that we get files data from cache, and returns a CArrayDataProvider, a CPagination object, and a CSort object
     *
     */
    public function testFormattedReturnsDataProvider()
    {
        $pageSize = 2;
        $orderBy = "t.name ASC";

        $expected = array( // only two items are expected as we've set the pageSize to be 2
            array(
                'sample_id' => 1,
                'linkName' => 'Sample 1',
                'dataset_id' => 1,
                'species_id' => 1,
                'tax_id' => 9238,
                'common_name' => 'Adelie penguin',
                'scientific_name' => 'Pygoscelis adeliae',
                'genbank_name' => 'Adelie penguin',
                'name' => "Sample 1",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'taxonomy_link' => '<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=9238">9238</a>',
                'sample_attributes' => array(
                    array("keyword" => "some value"),
                    array("number of lines" => "155"),
                ),
                'displayAttr' => "<span class=\"js-long-1\">Keyword:some value<br/>Number of lines:155<br/></span>",
            ),
            array(
                'sample_id' => 2,
                'linkName' => 'Sample 2',
                'dataset_id' => 1,
                'species_id' => 2,
                'tax_id' => 4555,
                'common_name' => 'Foxtail millet',
                'scientific_name' => 'Setaria italica',
                'genbank_name' => 'Foxtail millet',
                'name' => "Sample 2",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'taxonomy_link' => '<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=4555">4555</a>',
                'sample_attributes' => [],
                'displayAttr' => "",
            ),
            // array(
            //     'sample_id' => 3,
            //     'linkName' => 'Sample 3',
            //     'dataset_id' => 1,
            //     'species_id' => 1,
            //     'tax_id' => 9238,
            //     'common_name'=>'Adelie penguin',
            //     'scientific_name'=>'Pygoscelis adeliae',
            //     'genbank_name' => 'Adelie penguin',
            //     'name' => "Sample 3",
            //     'consent_document' => "",
            //     'submitted_id' => null,
            //     'submission_date' => null,
            //     'contact_author_name' => null,
            //     'contact_author_email' => null,
            //     'sampling_protocol' => null,
            //     'taxonomy_link' => '<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&amp;id=9238">9238</a>',
            //     'sample_attributes' => [],
            //     'displayAttr' => "",
            // ),
        );

        // create a mock for the CachedDatasetSamples
        $cachedDatasetSamples = $this->getMockBuilder(DatasetSamplesInterface::class)
                         ->setMethods(['getDatasetSamples','getDatasetDOI','getDatasetId','countDatasetSamples'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetSamples->expects($this->exactly(3))
                 ->method('getDatasetSamples')
                 ->willReturn($expected);

        $daoUnderTest = new FormattedDatasetSamples($pageSize, $cachedDatasetSamples);
        $this->assertEquals($expected, $daoUnderTest->getDataProvider()->getData()) ;
        $this->assertEquals(2, $daoUnderTest->getDataProvider()->getPagination()->getPageSize()) ;
        $this->assertEquals($orderBy, $daoUnderTest->getDataProvider()->getSort()->getOrderBy()) ;
    }
}
