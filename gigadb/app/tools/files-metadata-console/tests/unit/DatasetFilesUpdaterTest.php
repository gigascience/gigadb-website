<?php

use app\components\DatasetFilesUpdater;

/**
 * Tests DatasetFilesUpdater component
 */
class DatasetFilesUpdaterTest extends \Codeception\Test\Unit
{
    /**
     * Test that a list of dataset DOIs can be returned that need their file
     * URLs updating
     */
    public function testGetPendingDatasetsWithExcludedDois(): void
    {
        $batchSize = 3;
        $excludedDois = ['100003', '100004'];

        $dfu = DatasetFilesUpdater::build(true);
        $dois = $dfu->getNextPendingDatasets($batchSize, $excludedDois);
        codecept_debug($dois);
        $this->assertEquals(3, sizeof($dois), "Unexpected number of DOIs returned");
        $this->assertTrue(in_array("100002", $dois), "DOI 100002 was not found");
        $this->assertFalse(in_array("100003", $dois), "DOI 100003 should not have been returned");
    }
}