<?php

declare(strict_types=1);

class MintDoiTest extends CTestCase
{
    protected function setUp() {
        Yii::import('application.controllers.AdminDatasetController');
    }

    public function testHandleDoiCheckAndSetToPublishableWith200StatusCode()
    {
        $controller = new AdminDatasetController('adminDataset');

        $dataset = $this->getMockBuilder(Dataset::class)
                        ->setMethods(['save'])
                        ->getMock();
        $dataset->upload_status = 'Incomplete';
        $dataset->expects($this->once())->method('save')->willReturn(true);

        $isPresent = $controller->handleDoiCheckAndSetToPublishable($dataset, 200);

        $this->assertTrue($isPresent);
        $this->assertTrue($dataset->is_publishable);
        $this->assertEquals('ImportFromEM', $dataset->upload_status);
    }

    public function testHandleDoiCheckAndSetToPublishableWith204StatusCode()
    {
        $controller = new AdminDatasetController('adminDataset');

        $dataset = $this->getMockBuilder(Dataset::class)
                        ->setMethods(['save'])
                        ->getMock();
        $dataset->upload_status = 'Incomplete';
        $dataset->expects($this->once())->method('save')->willReturn(true);

        $isPresent = $controller->handleDoiCheckAndSetToPublishable($dataset, 204);

        $this->assertTrue($isPresent);
        $this->assertTrue($dataset->is_publishable);
        $this->assertEquals('ImportFromEM', $dataset->upload_status);
    }

    public function testHandleDoiCheckAndSetToPublishableWithoutAGoodStatusCode()
    {
        $controller = new AdminDatasetController('adminDataset');

        $dataset = $this->getMockBuilder(Dataset::class)
                        ->setMethods(['save'])
                        ->getMock();
        $dataset->upload_status = 'Complete';
        $dataset->expects($this->never())->method('save');

        $isPresent = $controller->handleDoiCheckAndSetToPublishable($dataset, 404);

        $this->assertFalse($isPresent);
        $this->assertObjectNotHasAttribute('is_publishable', $dataset);
    }
}
