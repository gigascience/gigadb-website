<?php
class DatasetSubmitterDataTest extends CDbTestCase
{
	protected $fixtures=array(
        'datasets'=>'Dataset',
    );

	public function setUp()
	{
		parent::setUp();
	}

	public function testStoredReturnsEmailAddress()
	{

		$doi = 100243;

		$dao_under_test = new StoredDatasetSubmitter($doi, $this->getFixtureManager()->getDbConnection() );
		$this->assertEquals("user@gigadb.org", $dao_under_test->getEmailAddress());

	}

	public function testCachedReturnsEmailAddressCacheHit()
	{

		$doi = 100243;
		//we first need to create a mock object for the cache
		$cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();
        //then we set our expecation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${doi}_submitterEmailAddress"))
                 ->willReturn("user@gigadb.org");

		$dao_under_test = new CachedDatasetSubmitter(
			$cache,
			new StoredDatasetSubmitter($doi, $this->getFixtureManager()->getDbConnection() )
		);

		$this->assertEquals("user@gigadb.org", $dao_under_test->getEmailAddress() );
	}


	public function testCachedReturnsEmailAddressCacheMiss()
	{
		$doi = 100243;
		//we first need to create a mock object for the cache
		$cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get','set'])
                         ->getMock();
        //then we set our expecation for a Cache Miss
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${doi}_submitterEmailAddress"))
                 ->willReturn(false);

        //when there is a cache miss, we also expect the value to be set into the cache for 24 hours
        $cache->expects($this->once())
                 ->method('set')
                 ->with(
                 	$this->equalTo("dataset_${doi}_submitterEmailAddress"),
                 	"user@gigadb.org",
                 	60*60*24
                 )
                 ->willReturn(false);

		$dao_under_test = new CachedDatasetSubmitter(
							$cache,
							new StoredDatasetSubmitter(
								$doi,
								$this->getFixtureManager()->getDbConnection()
							)
						);

		$this->assertEquals("user@gigadb.org", $dao_under_test->getEmailAddress() );
	}


	public function testAuthorisedReturnsEmailAddressAccepted()
	{
		$doi = 100243;
		//we first need to create a stub object for the cache
		$cache = $this->createMock(CApcCache::class);

        //then we set a stub for a Cache Hit
        $cache->method('get')
                 ->with( $this->equalTo("dataset_${doi}_submitterEmailAddress") )
                 ->willReturn("user@gigadb.org");

        //we need to create a mock for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->getMockBuilder(CWebUser::class)
                         ->setMethods(['getIsGuest'])
                         ->getMock();

        //we set the mocked method to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->expects($this->once())
                 ->method('getIsGuest')
                 ->willReturn(false);

		$dao_under_test = new AuthorisedDatasetSubmitter(
								$current_user,
								new CachedDatasetSubmitter(
									$cache,
									new StoredDatasetSubmitter(
										$doi,
										$this->getFixtureManager()->getDbConnection()
									)
								)
						);

		$this->assertEquals("user@gigadb.org", $dao_under_test->getEmailAddress() );
	}

	public function testAuthorisedReturnsEmailAddressDenied()
	{
		$doi = 100243;
		//we first need to create a stub object for the cache
		$cache = $this->createMock(CApcCache::class);

        //then we set a stub for a Cache Hit
        $cache->method('get')
                 ->with( $this->equalTo("dataset_${doi}_submitterEmailAddress") )
                 ->willReturn("user@gigadb.org");

        //we need to create a mock for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->getMockBuilder(CWebUser::class)
                         ->setMethods(['getIsGuest'])
                         ->getMock();

        //we set the mocked method to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->expects($this->once())
                 ->method('getIsGuest')
                 ->willReturn(true);

		$dao_under_test = new AuthorisedDatasetSubmitter(
								$current_user,
								new CachedDatasetSubmitter(
									$cache,
									new StoredDatasetSubmitter(
										$doi,
										$this->getFixtureManager()->getDbConnection()
									)
								)
						);

		$this->assertEquals("", $dao_under_test->getEmailAddress() );
	}
}