<?php

class FormattedDatasetLinksPreviewTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testGetDatasetId()
    {
        $expected_dataset_id = 1;

        $cachedDatasetLinksPreview = $this->getMockBuilder(CachedDatasetLinksPreview::class)
            ->setMethods(['getDatasetId'])
            ->disableOriginalConstructor()
            ->getMock();

        $cachedDatasetLinksPreview->expects($this->once())
            ->method('getDatasetId')
            ->willReturn(1);

        $controller = $this->createMock(CController::class);

        $idUnderTest = new FormattedDatasetLinksPreview($controller, $cachedDatasetLinksPreview);
        $this->assertEquals($expected_dataset_id, $idUnderTest->getDatasetId());

    }

    public function testGetDatasetDOI()
    {
        $expected_doi = '100243';

        $cachedDatasetLinksPreview = $this->getMockBuilder(CachedDatasetLinksPreview::class)
            ->setMethods(['getDatasetDOI'])
            ->disableOriginalConstructor()
            ->getMock();

        $cachedDatasetLinksPreview->expects($this->once())
            ->method('getDatasetDOI')
            ->willReturn('100243');

        $controller = $this->createMock(CController::class);

        $doiUnderTest = new FormattedDatasetLinksPreview($controller, $cachedDatasetLinksPreview);
        $this->assertEquals($expected_doi, $doiUnderTest->getDatasetDOI());
    }

    public function testGetPreviewDataForLinks()
    {
        $expected_FormatedData = array(
            array(
                'preview_title'=>'<a href="http://foo6.com">Exercise generates immune cells in bone</a>',
                'preview_description'=>'<p>Mechanosensing stem-cell niche promotes lymphocyte production.</p>',
                'preview_imageUrl'=>'<a href="http://foo6.com">'.'<img src="https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png" alt="Go to site"/></a>',
            )
        );

        $cachedDatasetLinksPreview = $this->getMockBuilder(CachedDatasetLinksPreview::class)
            ->setMethods(['getPreviewDataForLinks'])
            ->disableOriginalConstructor()
            ->getMock();

        $cachedDatasetLinksPreview->expects($this->once())
            ->method('getPreviewDataForLinks')
            ->willReturn(
                array(
                    array(
                        'preview_title'=>'<a href="http://foo6.com">Exercise generates immune cells in bone</a>',
                        'preview_description'=>'<p>Mechanosensing stem-cell niche promotes lymphocyte production.</p>',
                        'preview_imageUrl'=>'<a href="http://foo6.com">'.'<img src="https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png" alt="Go to site"/></a>',
                    )
                )
            );

        $controller = $this->createMock(CController::class);

        $formattedDataUnderTest = new FormattedDatasetLinksPreview($controller, $cachedDatasetLinksPreview);
        $this->assertEquals($expected_FormatedData, $formattedDataUnderTest->getPreviewDataForLinks());


    }
}