<?php

/**
 * Unit tests for FormattedDatasetExternalLinks to present external links associated to a dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetExternalLinksTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testFormattedReturnsDatasetId()
    {
        $dataset_id = 6;
        // create a mock for the CachedDatasetExternalLinks
        $cachedDatasetExternalLinks = $this->getMockBuilder(CachedDatasetExternalLinks::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetExternalLinks->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(6);


        $daoUnderTest = new FormattedDatasetExternalLinks($cachedDatasetExternalLinks);
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testFormattedReturnsDatasetDOI()
    {
        $dataset_id = 6;
        $doi = "100044";
         // create a mock for the CachedDatasetExternalLinks
        $cachedDatasetExternalLinks = $this->getMockBuilder(CachedDatasetExternalLinks::class)
                         ->setMethods(['getDatasetDOI'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetExternalLinks->expects($this->once())
                 ->method('getDatasetDOI')
                 ->willReturn("100044");


        $daoUnderTest = new FormattedDatasetExternalLinks($cachedDatasetExternalLinks);
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    public function testFormattedReturnsDatasetExternalLinksTypesNames()
    {
        $dataset_id = 1;
        // create a mock for the CachedDatasetExternalLinks
        $cachedDatasetExternalLinks = $this->getMockBuilder(CachedDatasetExternalLinks::class)
                         ->setMethods(['getDatasetExternalLinksTypesAndCount'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation
        $cachedDatasetExternalLinks->expects($this->once())
                 ->method('getDatasetExternalLinksTypesAndCount')
                 ->willReturn(
                     array(
                        "Additional information" => 2,
                        "Genome browser" => 1,
                        "Protocols.io" => 1,
                        "JBrowse" => 1,
                        "3D Models" => 1,
                     )
                 );

        $expected = array(
                        "Additional information" => "additionalinformation",
                        "Genome browser" => "genomebrowser",
                        "Protocols.io" => "protocolsio",
                        "JBrowse" => "jbrowse",
                        "3D Models" => "3dmodels",
                    ) ;

        $daoUnderTest = new FormattedDatasetExternalLinks($cachedDatasetExternalLinks);
        $this->assertEquals($expected, $daoUnderTest->getDatasetExternalLinksTypesNames()) ;
    }
}
