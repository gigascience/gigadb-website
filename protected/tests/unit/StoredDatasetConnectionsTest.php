<?php
/**
 * Unit tests for StoredDatasetConnections to retrieve from storage connected datasets (through relations and keywords)
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetConnectionsTest extends CDbTestCase
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

	public function testStoredReturnsDatasetId()
	{
		$dataset_id = 1;
		$daoUnderTest = new StoredDatasetConnections($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$this->assertEquals($dataset_id, $daoUnderTest->getDatasetId() ) ;
	}

	public function testStoredReturnsDatasetDOI()
	{
		$dataset_id = 1;
		$doi = 100243;
		$daoUnderTest = new StoredDatasetConnections($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$this->assertEquals($doi, $daoUnderTest->getDatasetDOI() ) ;
	}

	public function testStoredReturnsRelations()
	{
		$dataset_id = 6;
		$expected = array(
			// array(
			// 	'dataset_id'=>3, // 101001
			// 	'related_id'=>4, // 101000
			// 	'relationship_id'=>3, //IsSupplementTo
			// ),
			// array(
			// 	'dataset_id'=>4, // 101000
			// 	'related_id'=>3, // 101001
			// 	'relationship_id'=>4, //IsSupplementedBy
			// ),
			// array(
			// 	'dataset_id'=>5, // 100038
			// 	'related_id'=>6, // 100044
			// 	'relationship_id'=>17, //IsCompiledBy
			// ),
			array(
				'dataset_id'=>6, // 100044
				'related_id'=>5, // 100038
				'relationship'=>"Compiles", //18 Compiles
			),
			array(
				'dataset_id'=>6, // 100044
				'related_id'=>7, // 100148
				'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
			),
			// array(
			// 	'dataset_id'=>7, // 100148
			// 	'related_id'=>6, // 100044
			// 	'relationship_id'=>9, //IsNewVersionOf
			// ),
		);

		$daoUnderTest = new StoredDatasetConnections($dataset_id, $this->getFixtureManager()->getDbConnection()) ;
		$this->assertEquals($expected, $daoUnderTest->getRelations());
		$this->assertEquals([$expected[1]], $daoUnderTest->getRelations("IsPreviousVersionOf"));
		$this->assertEquals([$expected[0]], $daoUnderTest->getRelations("Compiles"));
		$this->assertEquals([], $daoUnderTest->getRelations("DoesNotExistRelationship"));


	}
}
?>