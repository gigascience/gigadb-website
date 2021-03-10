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

}