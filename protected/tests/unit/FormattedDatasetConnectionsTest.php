<?php
/**
 * Unit tests for FormattedDatasetConnections to present to the dataset view main dataset info
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetConnectionsTest extends CDbTestCase
{
    protected $fixtures=array( //careful, the order matters here because of foreign key constraints
        'publishers'=>'Publisher',
        'relationships'=>'Relationship',
        'datasets'=>'Dataset',
        'relations'=>'Relation',

    );

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
                                            'dataset_id'=>6, // 100044
                                            'dataset_doi'=>"100044", // 100044
                                            'related_id'=>5, // 100038
                                            'related_doi'=>"100038", // 100038
                                            'relationship'=>"Compiles", //18 Compiles
                                        ),
                                        array(
                                            'dataset_id'=>6, // 100044
                                            'dataset_doi'=>"100044", // 100044
                                            'related_id'=>7, // 100148
                                            'related_doi'=>"100148", // 100148
                                            'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
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
                    Yii::getPathOfAlias("application")."/views/dataset/_connection_IsPreviousVersionOf.php",
                    array("relation" => array(
                            'dataset_doi'=>"100044", // 6
                            'related_doi'=>"100148", // 7
                            'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
                            'full_dataset_doi'=>"10.5072/100044",
                            'full_related_doi'=>"10.5072/100148",
                        )
                    ),
                    true
                )
                ->willReturn("special HTML code for IsPreviousVersionOf");

        $expected = array(
                        array(
                            'dataset_doi'=>"100044", // 6
                            'related_doi'=>"100038", // 5
                            'relationship'=>"Compiles", //18 Compiles
                            'extra_html' => "",
                            'full_dataset_doi'=>"10.5072/100044",
                            'full_related_doi'=>"10.5072/100038",
                        ),
                        array(
                            'dataset_doi'=>"100044", // 6
                            'related_doi'=>"100148", // 7
                            'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
                            'extra_html' => "special HTML code for IsPreviousVersionOf",
                            'full_dataset_doi'=>"10.5072/100044",
                            'full_related_doi'=>"10.5072/100148",
                        )
                    );

        $daoUnderTest = new FormattedDatasetConnections($controller, $cachedDatasetConnections);
        $this->assertEquals( $expected, $daoUnderTest->getRelations() );
    }



}