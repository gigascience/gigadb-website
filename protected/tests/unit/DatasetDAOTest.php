<?php

class DatasetDAOTest extends CDbTestCase {


	protected $fixtures=array(
        'dataset_attributes'=>'DatasetAttributes',
    );


	/**
	 * test that keywords in the database are removed
	 *
	 */
    public function testItShouldRemoveKeywordsFromDatabase()
    {
    	$dataset_id = 1;
    	$keyword_attribute_id = 1;

    	$dataset_dao = new DatasetDAO();

    	$dataset_dao->removeKeywordsFromDatabaseForDatasetId($dataset_id);

    	$this->assertEquals(
    		0,
    		DatasetAttributes::model()->count(
    			"dataset_id = :dataset_id and attribute_id = :attribute_id",
    			array(':dataset_id'=>$dataset_id, ':attribute_id'=>$keyword_attribute_id )
    		)
    	);
	}

	/**
	 * test that keywords are added into the database
	 *
	 * @dataProvider keywordsProvider
	 */
    public function testItShouldAddKeywordsToDatabase($post_keywords)
    {
    	$dataset_id = 1;
    	$keyword_attribute_id = 1;
    	$keywords_array = array_filter(explode(",",$post_keywords));
    	$nb_of_keywords = count($keywords_array);

    	// Create a mock for the DatasetAttributesFactory class,
        $da_factory = $this->getMockBuilder(DatasetAttributesFactory::class)
                        ->setMethods(['setAttributeId', 'setDatasetId', 'setValue', 'create','save'])
                        ->getMock();


        // Instantiate a new DatasetDAO, the system under test.
        $dataset_dao = new DatasetDAO();
        $dataset_dao->setDatasetAttrFactory($da_factory);

        // Below, we expect a new DatasetAttribute object created, set and saved for each keyword.
        // Expected number of calls to be exactly zero times, two times and two times respectively
        // for each keywordsProvider dataset, matching the number of keywords in each.

        $da_factory->expects( $this->exactly($nb_of_keywords) )
        	->method('create');

        $da_factory->expects(  $this->exactly($nb_of_keywords) )
                ->method('setDatasetId')
                ->with($this->equalTo($dataset_id));

        $da_factory->expects(  $this->exactly($nb_of_keywords) )
                ->method('setAttributeId')
                ->with($this->equalTo($keyword_attribute_id));

        $da_factory->expects( $this->exactly($nb_of_keywords) )
                ->method('setValue')
                ->withConsecutive( //we expect setValue to be called twice consecutively: once for each keyword, trimmed
                	[ trim($keywords_array[0]) ],
                	[ trim($keywords_array[1]) ]
                );

        $da_factory->expects( $this->exactly($nb_of_keywords) )
        		->method('save');


       	// Execute the method to test
    	$dataset_dao->addKeywordsToDatabaseForDatasetIdAndString($dataset_id, $post_keywords);


    }

    /**
     * test function to transition Dataset from one status to another
     */
    public function testTransitionStatus()
    {
        $datasetDAO = new DatasetDAO();

        $datasetDAO->setIdentifier("100243");
        $success = $datasetDAO->transitionStatus("Published","AssigningFTPBox","foobar");

        $datasetDAO->setIdentifier("100249");
        $failure = $datasetDAO->transitionStatus("Pending","AssigningFTPBox","foobar");

        $this->assertTrue($success);
        $this->assertFalse($failure);
        $changedDataset = Dataset::model()->findByAttributes(["identifier" => "100243"]);
        $this->assertEquals("AssigningFTPBox", $changedDataset->upload_status);

    }

    /**
     * test passing properties to constructor
    */
    public function testConstructor()
    {
        $datasetDAO = new DatasetDAO(["identifier" => "100249"]);
        $this->assertEquals("100249", $datasetDAO->getIdentifier());
    }

    /**
     * test retrieve title and status
     */
    public function testGetTitleAndStatus()
    {
        $datasetDAO = new DatasetDAO(["identifier" => "100249"]);
        $this->assertEquals('Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',$datasetDAO->getTitleAndStatus()['title']);

        $this->assertEquals('Published',$datasetDAO->getTitleAndStatus()['status']);
    }


    public function keywordsProvider()
    {
    	return [
    		"no keyword" => [""],
    		"two new keywords" => ["bam, dam"],
    		"same keywords" => ["am,gram "],
    	];
    }

}

?>