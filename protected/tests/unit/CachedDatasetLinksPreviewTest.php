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

    public function testGetPreviewDataForLinksCacheHit()
    {
        $dataset_id = 2;
        $expected_previewData = array(
            array(
                'short_doi'=>'100249',
                'external_url'=>'http://foo6.com',
                'type'=>'3D Models',
                'external_title'=>'Exercise generates immune cells in bone',
                'external_description'=>'Mechanosensing stem-cell niche promotes lymphocyte production.',
                'external_imageUrl'=>'https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png',
            )
        );

        //Assume the external website info was cached, so no need to mock GetPreviewDataForLinks
        $storedDatasetLinksPreview = $this->createMock(StoredDatasetLinksPreview::class);
        $storedDatasetLinksPreview->method('getDatasetId')
            ->willReturn(2);

        $cache = $this->getMockBuilder(CApcCache::class)
            ->setMethods(['get'])
            ->getMock();

        $cache->expects($this->once())
            ->method('get')
            //mock the cached returned message
            ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetLinksPreview_getPreviewDataForLinks"))
            ->willReturn(
                array(
                    array(
                        'short_doi'=>'100249',
                        'external_url'=>'http://foo6.com',
                        'type'=>'3D Models',
                        'external_title'=>'Exercise generates immune cells in bone',
                        'external_description'=>'Mechanosensing stem-cell niche promotes lymphocyte production.',
                        'external_imageUrl'=>'https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png',
                    )
                )
            );

        $cacheDependency = $this->createMock(CCacheDependency::class);

        $previewDataUnderTest = new CachedDatasetLinksPreview($cache, $cacheDependency, $storedDatasetLinksPreview);
        $this->assertEquals($expected_previewData, $previewDataUnderTest->getPreviewDataForLinks());
    }

    public function testtestGetPreviewDataForLinksCacheMiss()
    {
        $dataset_id = 2;
        $expected_previewData = array(
            array(
                'short_doi'=>'100249',
                'external_url'=>'http://foo6.com',
                'type'=>'3D Models',
                'external_title'=>'Exercise generates immune cells in bone',
                'external_description'=>'Mechanosensing stem-cell niche promotes lymphocyte production.',
                'external_imageUrl'=>'https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png',
            )
        );

        //Assume external web site info was not cached, so need to mock GetPreviewDataForLinks
        $storedDatasetLinksPreview = $this->getMockBuilder(StoredDatasetLinksPreview::class)
            ->setMethods(['getDatasetId', 'getPreviewDataForLinks'])
            ->disableOriginalConstructor()
            ->getMock();

        $storedDatasetLinksPreview->expects($this->exactly(2))
            ->method('getDatasetId')
            ->willReturn(2);

        $storedDatasetLinksPreview->expects($this->once())
            ->method('getPreviewDataForLinks')
            ->willReturn(
                array(
                    array(
                        'short_doi'=>'100249',
                        'external_url'=>'http://foo6.com',
                        'type'=>'3D Models',
                        'external_title'=>'Exercise generates immune cells in bone',
                        'external_description'=>'Mechanosensing stem-cell niche promotes lymphocyte production.',
                        'external_imageUrl'=>'https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png',
                    )
                )
            );

        $cacheDependency = $this->createMock(CCacheDependency::class);

        $cache = $this->getMockBuilder(CApcCache::class)
            ->setMethods(['get','set'])
            ->getMock();

        //set a Cache Miss
        $cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetLinksPreview_getPreviewDataForLinks"))
            ->willReturn(
                false
            );

        $cache->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo("dataset_${dataset_id}_CachedDatasetLinksPreview_getPreviewDataForLinks"),
                array(
                    array(
                        'short_doi'=>'100249',
                        'external_url'=>'http://foo6.com',
                        'type'=>'3D Models',
                        'external_title'=>'Exercise generates immune cells in bone',
                        'external_description'=>'Mechanosensing stem-cell niche promotes lymphocyte production.',
                        'external_imageUrl'=>'https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png',
                    )
                ),
                Cacheable::defaultTTL*30,
                $cacheDependency
            )
            ->willReturn(true);

        $previewDataUnderTest = new CachedDatasetLinksPreview($cache, $cacheDependency, $storedDatasetLinksPreview);
        $this->assertEquals($expected_previewData, $previewDataUnderTest->getPreviewDataForLinks());
    }
}