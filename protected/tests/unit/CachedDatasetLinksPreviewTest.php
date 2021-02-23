<?php

class CachedDatasetLinksPreviewTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testGetDatasetId()
    {
        $dataset_id = 1;

        $storeDatasetLinksPreview = $this->getMockBuilder(StoredDatasetLinksPreview::class)
            ->setMethods(['getDatasetId'])
            ->disableOriginalConstructor()
            ->getMock();
//        $storeDatasetLinksPreview = $this->createMock(StoredDatasetLinksPreview::class);
        $storeDatasetLinksPreview->method('getDatasetId')
            ->willReturn(1);

        file_put_contents('test_link_mock.txt', print_r($storeDatasetLinksPreview, true));

        $cache = $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $idUnderTest = new CachedDatasetLinksPreview($cache, $cacheDependency, $storeDatasetLinksPreview);
        $this->assertEquals($dataset_id, $idUnderTest->getDatasetId());

    }
}