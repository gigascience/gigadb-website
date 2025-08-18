<?php

declare(strict_types=1);

namespace unit;

use GigaDB\services\UploadStatusWorkflowService;
use PHPUnit\Framework\TestCase;

/**
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run unit UploadStatusWorkflowServiceTest
 */
class UploadStatusWorkflowServiceTest extends TestCase
{
    private UploadStatusWorkflowService $uploadStatusWorkflowService;

    protected function setup()
    {
        $this->uploadStatusWorkflowService = new UploadStatusWorkflowService();

    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @dataProvider dataProviderForUploadStatus
     */
    public function testTransitionStatusWithPreviousStatus($previousStatus, $toStatus, $fromStatus)
    {
        $result = $this->uploadStatusWorkflowService->transitionStatus($fromStatus, $toStatus, null, $previousStatus);

        $this->assertEquals($previousStatus === $fromStatus, $result);
    }

    /**
     * @dataProvider dataProviderForUploadStatus
     */
    public function testTransitionStatusWithIdentifier($previousStatus, $toStatus, $fromStatus)
    {
        $datasetMock = \Mockery::mock('alias:GigaDB\models\Dataset');
        $datasetMock->upload_status = $previousStatus;

        $datasetMock->shouldReceive('save')
            ->andReturn(true);
        $queryMock = \Mockery::mock();
        $queryMock->shouldReceive('where')
            ->with(['identifier' => 1])
            ->andReturn($queryMock);
        $queryMock->shouldReceive('one')
            ->andReturn($datasetMock);

        $datasetMock->shouldReceive('find')
            ->andReturn($queryMock);

        $result = $this->uploadStatusWorkflowService->transitionStatus($fromStatus, $toStatus, 1);

        $this->assertEquals($previousStatus === $fromStatus, $result);

    }

    public function dataProviderForUploadStatus()
    {
        return [
            ['Submitted', 'DataAvailableForReview', 'Submitted'],
            ['DataAvailableForReview', 'Submitted', 'DataAvailableForReview'],
            ['UserProvidedData', 'Submitted', 'DataAvailableForReview'],
            ['UserProvidedData', 'DataAvailableForReview', 'UserProvidedData'],
            ['DataAvailableForReview', 'DataPending', 'Submitted'],
            ['Submitted', 'DataPending', 'Submitted'],
        ];
    }
}
