<?php

/**
 * Unit tests for FormattedDatasetConnections to present to the dataset view resources connected to that dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetConnectionsTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * test that the getRelations($type) returns the HTML snippet for the appropriate type of relations
     * here, for the "IsPreviousVersionOf"
     */
    public function testFormattedReturnsIsPreviousVersionOfRelationship()
    {
        $dataset_id = 6;
        // making a mock of CachedDatasetConnections because expects it to be passed a getRelations(...) message
        $cachedDatasetConnections = $this->getMockBuilder(CachedDatasetConnections::class)
                                        ->setMethods(['getRelations'])
                                        ->disableOriginalConstructor()
                                        ->getMock();

        $cachedDatasetConnections->expects($this->once())
                                ->method('getRelations')
                                ->willReturn(
                                    array(
                                        array(
                                            'dataset_id' => 6, // 100044
                                            'dataset_doi' => "100044", // 100044
                                            'related_id' => 5, // 100038
                                            'related_doi' => "100038", // 100038
                                            'relationship' => "Compiles", //18 Compiles
                                        ),
                                        array(
                                            'dataset_id' => 6, // 100044
                                            'dataset_doi' => "100044", // 100044
                                            'related_id' => 7, // 100148
                                            'related_doi' => "100148", // 100148
                                            'relationship' => "IsPreviousVersionOf", //10 IsPreviousVersionOf
                                        )
                                    )
                                );

        // we need to make a mock of CController as we expect it be passed a render_file(...) message
        $controller = $this->getMockBuilder(CController::class)
                                        ->setMethods(['renderFile'])
                                        ->disableOriginalConstructor()
                                        ->getMock();
        $controller->expects($this->once())
                ->method('renderFile')
                ->with(
                    Yii::getPathOfAlias("application") . "/views/dataset/_connection_IsPreviousVersionOf.php",
                    array("relation" => array(
                            'dataset_doi' => "100044", // 6
                            'related_doi' => "100148", // 7
                            'relationship' => "IsPreviousVersionOf", //10 IsPreviousVersionOf
                            'full_dataset_doi' => "10.5072/100044",
                            'full_related_doi' => "10.5072/100148",
                        )
                    ),
                    true
                )
                ->willReturn("special HTML code for IsPreviousVersionOf");

        $expected = array(
                        array(
                            'dataset_doi' => "100044", // 6
                            'related_doi' => "100038", // 5
                            'relationship' => "Compiles", //18 Compiles
                            'extra_html' => "",
                            'full_dataset_doi' => "10.5072/100044",
                            'full_related_doi' => "10.5072/100038",
                        ),
                        array(
                            'dataset_doi' => "100044", // 6
                            'related_doi' => "100148", // 7
                            'relationship' => "IsPreviousVersionOf", //10 IsPreviousVersionOf
                            'extra_html' => "special HTML code for IsPreviousVersionOf",
                            'full_dataset_doi' => "10.5072/100044",
                            'full_related_doi' => "10.5072/100148",
                        )
                    );

        $daoUnderTest = new FormattedDatasetConnections($controller, $cachedDatasetConnections);
        $this->assertEquals($expected, $daoUnderTest->getRelations());
    }

    public function testFormattedReturnsPublications()
    {

        $dataset_id = 6;

        // creating a stub for the controller
        $controller = $this-> createMock(CController::class);

        // making a mock of CachedDatasetConnections because expects it to be passed a getPublications(...) message
        $cachedDatasetConnections = $this->getMockBuilder(CachedDatasetConnections::class)
                                        ->setMethods(['getPublications'])
                                        ->disableOriginalConstructor()
                                        ->getMock();

        $cachedDatasetConnections->expects($this->once())
                                ->method('getPublications')
                                ->willReturn(
                                    array(
                                        array(
                                            'id' => 1,
                                            'identifier' => "10.1186/gb-2012-13-10-r100",
                                            'pmid' => 23075480,
                                            'dataset_id' => 1,
                                            'citation' => "full citation fetched remotely. doi:10.1186/gb-2012-13-10-r100",
                                            'pmurl' => "http://www.ncbi.nlm.nih.gov/pubmed/23075480",
                                        ),
                                        array(
                                            'id' => 2,
                                            'identifier' => "10.1038/nature10158",
                                            'pmid' => null,
                                            'dataset_id' => 1,
                                            'citation' => "Another full citation fetched remotely. doi:10.1038/nature10158",
                                            'pmurl' => null,
                                        ),
                                    )
                                );

        $expected = array(
                        array(
                            'id' => 1,
                            'identifier' => "10.1186/gb-2012-13-10-r100",
                            'pmid' => 23075480,
                            'dataset_id' => 1,
                            'citation' => 'full citation fetched remotely. <a href="https://doi.org/10.1186/gb-2012-13-10-r100">doi:10.1186/gb-2012-13-10-r100</a>',
                            'pmurl' => '(PubMed:<a href="http://www.ncbi.nlm.nih.gov/pubmed/23075480">23075480</a>)',
                        ),
                        array(
                            'id' => 2,
                            'identifier' => "10.1038/nature10158",
                            'pmid' => null,
                            'dataset_id' => 1,
                            'citation' => 'Another full citation fetched remotely. <a href="https://doi.org/10.1038/nature10158">doi:10.1038/nature10158</a>',
                            'pmurl' => null,
                        ),
                    );
        $daoUnderTest = new FormattedDatasetConnections($controller, $cachedDatasetConnections);
        $this->assertEquals($expected, $daoUnderTest->getPublications());
    }

    public function testFormattedReturnsProjects()
    {
        $dataset_id = 1;

        // creating a stub for the controller
        $controller = $this-> createMock(CController::class);

        // making a mock of CachedDatasetConnections because expects it to be passed a getPublications(...) message
        $cachedDatasetConnections = $this->getMockBuilder(CachedDatasetConnections::class)
                                        ->setMethods(['getProjects'])
                                        ->disableOriginalConstructor()
                                        ->getMock();

        $cachedDatasetConnections->expects($this->once())
                                ->method('getProjects')
                                ->willReturn(
                                    array(
                                        array(
                                            'id' => 1,
                                            'url' => "http://avian.genomics.cn/en/index.html",
                                            'name' => "The Avian Phylogenomic Project",
                                            'image_location' => "http://gigadb.org/images/project/phylogenomiclogo.png",
                                        ),
                                        array(
                                            'id' => 2,
                                            'url' => "http://www.genome10k.org/",
                                            'name' => "Genome 10K",
                                            'image_location' => null,
                                        ),
                                    )
                                );
        $expected = array(
                        array(
                            'url' => "http://avian.genomics.cn/en/index.html",
                            'name' => "The Avian Phylogenomic Project",
                            'image_location' => "http://gigadb.org/images/project/phylogenomiclogo.png",
                            'format' => '<a href="http://avian.genomics.cn/en/index.html"><img src="http://gigadb.org/images/project/phylogenomiclogo.png" alt="Go to The Avian Phylogenomic Project website"/></a>',
                        ),
                        array(
                            'url' => "http://www.genome10k.org/",
                            'name' => "Genome 10K",
                            'image_location' => null,
                            'format' => '<a href="http://www.genome10k.org/">Genome 10K</a>'
                        ),
        );

        $daoUnderTest = new FormattedDatasetConnections($controller, $cachedDatasetConnections);
        $this->assertEquals($expected, $daoUnderTest->getProjects());
    }
}
