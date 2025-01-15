<?php

class DatasetSubmitterDataTest extends CDbTestCase
{
    protected $fixtures = array(
        'datasets' => 'Dataset',
    );

    public function setUp()
    {
        parent::setUp();
    }

    public function testStoredReturnsDatasetId()
    {
        $dataset_id = 1;
        $daoUnderTest = new StoredDatasetSubmitter($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testStoredReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = 100243;
        $daoUnderTest = new StoredDatasetSubmitter($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    public function testStoredReturnsEmailAddress()
    {

        $dataset_id = 1;

        $dao_under_test = new StoredDatasetSubmitter($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals("user@gigadb.org", $dao_under_test->getEmailAddress());
    }

    public function testCachedReturnsEmailAddressCacheHit()
    {

        $dataset_id = 1;
        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();
        //then we set our expecation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetSubmitter_getEmailAddress"))
                 ->willReturn("user@gigadb.org");

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $dao_under_test = new CachedDatasetSubmitter(
            $cache,
            $cacheDependency,
            new StoredDatasetSubmitter($dataset_id, $this->getFixtureManager()->getDbConnection())
        );

        $this->assertEquals("user@gigadb.org", $dao_under_test->getEmailAddress());
    }


    public function testCachedReturnsEmailAddressCacheMiss()
    {
        $dataset_id = 1;
        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get','set'])
                         ->getMock();
        //then we set our expecation for a Cache Miss
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetSubmitter_getEmailAddress"))
                 ->willReturn(false);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        //when there is a cache miss, we also expect the value to be set into the cache for 24 hours
        $cache->expects($this->once())
                 ->method('set')
                 ->with(
                     $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetSubmitter_getEmailAddress"),
                     "user@gigadb.org",
                     Cacheable::defaultTTL * 30,
                     $cacheDependency
                 )
                 ->willReturn(false);

        $dao_under_test = new CachedDatasetSubmitter(
            $cache,
            $cacheDependency,
            new StoredDatasetSubmitter(
                $dataset_id,
                $this->getFixtureManager()->getDbConnection()
            )
        );

        $this->assertEquals("user@gigadb.org", $dao_under_test->getEmailAddress());
    }


    public function testAuthorisedReturnsEmailAddressAccepted()
    {
        $dataset_id = 1;
        //we first need to create a stub object for CachedDatasetSubmitter
        $cachedDatasetSubmitter = $this->createMock(CachedDatasetSubmitter::class);

        //then we set a stub method for CachedDatasetSubmitter
        $cachedDatasetSubmitter->method('getEmailAddress')
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
            $cachedDatasetSubmitter
        );

        $this->assertEquals("user@gigadb.org", $dao_under_test->getEmailAddress());
    }

    public function testAuthorisedReturnsEmailAddressDenied()
    {
        $dataset_id = 1;
        //we first need to create a stub object for CachedDatasetSubmitter
        $cachedDatasetSubmitter = $this->createMock(CachedDatasetSubmitter::class);

        //then we set a stub method for CachedDatasetSubmitter
        $cachedDatasetSubmitter->method('getEmailAddress')
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
            $cachedDatasetSubmitter
        );

        $this->assertEquals("", $dao_under_test->getEmailAddress());
    }
}
