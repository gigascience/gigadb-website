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

        //create mock object for calling the method
        $storedDatasetLinksPreview = $this->getMockBuilder(StoredDatasetLinksPreview::class)
            ->setMethods(['getDatasetId'])
            ->disableOriginalConstructor()
            ->getMock();

        //expect the mock method to return 1
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

        //expect the mock method to return '100243'
        $storedDatasetLinksPreview->method('getDatasetDOI')
            ->willReturn('100243');

        $cache = $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $doiUnderTest = new CachedDatasetLinksPreview($cache, $cacheDependency, $storedDatasetLinksPreview);
        $this->assertEquals($expected_doi, $doiUnderTest->getDatasetDOI());

    }

    public function testGetImageUrlCacheHit()
    {
        $dataset_id = 1;
        $expected_imageurl = array(
            array(
                'url'=>'http://gigadb.org/images/data/cropped/100243.gif',
                ),
            );

        $storedDatasetLinksPreview = $this->createMock(StoredDatasetLinksPreview::class);
        $storedDatasetLinksPreview->method('getDatasetId')
            ->willReturn(1);

        //create mock cache object that get the cached url
        $cache = $this->getMockBuilder(CApcCache::class)
            ->setMethods(['get'])
            ->getMock();

        $cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetLinksPreview_getImageUrl"))
            ->willReturn(array(
                array(
                    'url'=>'http://gigadb.org/images/data/cropped/100243.gif',
                    ),
                )
            );

        $cacheDependency = $this->createMock(CCacheDependency::class);

        $imageUrlUnderTest = new CachedDatasetLinksPreview($cache, $cacheDependency, $storedDatasetLinksPreview);
        $this->assertEquals($expected_imageurl, $imageUrlUnderTest->getImageUrl());
    }

    public function testGetImageUrlCacheMiss()
    {
        $dataset_id = 1;
        $expected_imageurl = array(
            array(
                'url'=>'http://gigadb.org/images/data/cropped/100243.gif',
            ),
        );

        //create mock StoredDatasetLinksPreview object to call getDatasetId and getImageUrl methods for the caching
        $storedDatasetLinksPreview = $this->getMockBuilder(StoredDatasetLinksPreview::class)
            ->setMethods(['getDatasetId', 'getImageUrl'])
            ->disableOriginalConstructor()
            ->getMock();
        $storedDatasetLinksPreview->expects($this->exactly(2))
            ->method('getDatasetId')
            ->willReturn(1);
        $storedDatasetLinksPreview->expects($this->once())
            ->method('getImageUrl')
            ->willReturn(
                array(
                    array(
                        'url'=>'http://gigadb.org/images/data/cropped/100243.gif',
                    ),
                )
            );

        //create mock cache object
        $cache = $this->getMockBuilder(CApcCache::class)
            ->setMethods(['set', 'get'])
            ->getMock();

        //expect cannot get image url from cache
        $cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetLinksPreview_getImageUrl"))
            ->willReturn(false);

        //so set the image url to cache
        $cache->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo("dataset_${dataset_id}_CachedDatasetLinksPreview_getImageUrl"),
                array(
                    array(
                        'url'=>'http://gigadb.org/images/data/cropped/100243.gif',
                    ),
                )
            )
            ->willReturn(true);


        $cacheDependency = $this->createMock(CCacheDependency::class);

        $imageUrlUnderTest = new CachedDatasetLinksPreview($cache, $cacheDependency, $storedDatasetLinksPreview);
        $this->assertEquals($expected_imageurl, $imageUrlUnderTest->getImageUrl());
    }
}