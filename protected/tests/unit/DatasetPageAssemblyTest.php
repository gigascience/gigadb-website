<?php


class DatasetPageAssemblyTest extends CDbTestCase
{
	public function testAssemble()
	{
		$mockApp = $this->createMock(CApplication::class);
		$d = new Dataset();
		$assembly = DatasetPageAssembly::assemble($d, $mockApp);
		$this->assertNotNull($assembly);
		$this->assertTrue(is_a($assembly,"DatasetPageAssembly"));
	}

	public function testDataset()
	{
		$app = Yii::app() ;
		$d = Dataset::model()->findByPk(1);

		$assembly = DatasetPageAssembly::assemble($d, $app);
		$this->assertEquals($d, $assembly->getDataset());
	}

	public function testDatasetSubmitter()
	{
		$app = Yii::app() ;
		$d = Dataset::model()->findByPk(1);

		$assembly = DatasetPageAssembly::assemble($d, $app);
		$this->assertEquals($assembly, $assembly->setDatasetSubmitter());
		$this->assertNotNull($assembly->getDatasetSubmitter());
		$this->assertTrue(is_a($assembly->getDatasetSubmitter(),"DatasetSubmitterInterface"));

	}

	public function testDatasetAccessions()
	{
		$app = Yii::app() ;
		$d = Dataset::model()->findByPk(1);

		$assembly = DatasetPageAssembly::assemble($d, $app);
		$this->assertEquals($assembly, $assembly->setDatasetAccessions());
		$this->assertNotNull($assembly->getDatasetAccessions());
		$this->assertTrue(is_a($assembly->getDatasetAccessions(),"DatasetAccessionsInterface"));

	}

	public function testDatasetMainSection()
	{
		$app = Yii::app() ;
		$d = Dataset::model()->findByPk(1);

		$assembly = DatasetPageAssembly::assemble($d, $app);
		$this->assertEquals($assembly, $assembly->setDatasetMainSection());
		$this->assertNotNull($assembly->getDatasetMainSection());
		$this->assertTrue(is_a($assembly->getDatasetMainSection(),"DatasetMainSectionInterface"));

	}

	public function testDatasetConnections()
	{
		$mockApp = $this->createMock(CApplication::class) ;
		$mockController = $this->createMock(CController::class) ;
		$mockCache = $this->createMock(CCache::class) ;
		$mockDb = $this->createMock(CDbConnection::class) ;
		$mockApp->expects(  $this->once() )
                ->method('getController') //because not set in tests we need mock setup here
                ->willReturn($mockController);

		$mockApp->expects(  $this->once() )
                ->method('getCache')
                ->willReturn($mockCache);

		$mockApp->expects(  $this->once() )
                ->method('getDb')
                ->willReturn($mockDb);

		$d = Dataset::model()->findByPk(1);

		$assembly = DatasetPageAssembly::assemble($d, $mockApp);
		$this->assertEquals($assembly, $assembly->setDatasetConnections());
		$this->assertNotNull($assembly->getDatasetConnections());
		$this->assertTrue(is_a($assembly->getDatasetConnections(),"DatasetConnectionsInterface"));

	}

	public function testDatasetExternalLinks()
	{
		$app = Yii::app() ;
		$d = Dataset::model()->findByPk(1);

		$assembly = DatasetPageAssembly::assemble($d, $app);
		$this->assertEquals($assembly, $assembly->setDatasetExternalLinks());
		$this->assertNotNull($assembly->getDatasetExternalLinks());
		$this->assertTrue(is_a($assembly->getDatasetExternalLinks(),"DatasetExternalLinksInterface"));

	}

	public function testDatasetFilesStored()
	{
		$app = Yii::app() ;
		$d = Dataset::model()->findByPk(1);
		$pageSize = 10;

		$assembly = DatasetPageAssembly::assemble($d, $app);
		$this->assertEquals($assembly, $assembly->setDatasetFiles($pageSize, "stored"));
		$this->assertNotNull($assembly->getDatasetFiles());
		$this->assertTrue(is_a($assembly->getDatasetFiles(),"DatasetFilesInterface"));

	}

	public function testDatasetSamples()
	{
		$app = Yii::app() ;
		$d = Dataset::model()->findByPk(1);
		$pageSize = 10;

		$assembly = DatasetPageAssembly::assemble($d, $app);
		$this->assertEquals($assembly, $assembly->setDatasetSamples($pageSize));
		$this->assertNotNull($assembly->getDatasetSamples());
		$this->assertTrue(is_a($assembly->getDatasetSamples(),"DatasetSamplesInterface"));

	}

	public function testSearchForm()
	{
		$app = Yii::app() ;
		$d = Dataset::model()->findByPk(1);

		$assembly = DatasetPageAssembly::assemble($d, $app);
		$this->assertEquals($assembly, $assembly->setSearchForm());
		$this->assertNotNull($assembly->getSearchForm());
		$this->assertTrue(is_a($assembly->getSearchForm(),"SearchForm"));

	}
}
?>