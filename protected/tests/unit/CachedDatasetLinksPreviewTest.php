<?php

class CachedDatasetLinksPreviewTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testGetDatasetId()
    {
        $expected_dataset_id = 1;

        $storedDatasetLinksPreview = $this->getMockBuilder(StoredDatasetLinksPreview::class)
            ->setMethods(['getDatasetId'])
            ->disableOriginalConstructor()
            ->getMock();

        $storedDatasetLinksPreview->method('getDatasetId')
            ->willReturn(1);

        $cache = $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $idUnderTest = new CachedDatasetLinksPreview($cache, $cacheDependency, $storedDatasetLinksPreview);
        $this->assertEquals($expected_dataset_id, $idUnderTest->getDatasetId());

    }

    public function testGetDatasetDOI()
    {
        $expected_doi = '100243';

        $storedDatasetLinksPreview = $this->getMockBuilder(StoredDatasetLinksPreview::class)
            ->setMethods(['getDatasetDOI'])
            ->disableOriginalConstructor()
            ->getMock();

        $storedDatasetLinksPreview->method('getDatasetDOI')
            ->willReturn('100243');

        $cache = $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $doiUnderTest = new CachedDatasetLinksPreview($cache, $cacheDependency, $storedDatasetLinksPreview);
        $this->assertEquals($expected_doi, $doiUnderTest->getDatasetDOI());

    }
}