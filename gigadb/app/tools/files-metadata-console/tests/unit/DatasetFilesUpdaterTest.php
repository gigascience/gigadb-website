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
    public function testGetPendingDatasets(): void
    {
        $batchSize = 3;

        $dfu = DatasetFilesUpdater::build(true);
        $dois = $dfu->getNextPendingDatasets($batchSize);
        codecept_debug($dois);
        $this->assertEquals(3, sizeof($dois), "Unexpected number of DOIs returned");
        $this->assertTrue(in_array("100004", $dois), "DOI 100004 was not found");
    }

    /**
     * Test that a list of dataset DOIs can be returned that need their file
     * URLs updating and does not contain excluded DOIs
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

    /**
     * Test file URLs for dataset DOI 100002 can be updated
     */
    public function testReplaceFileUrlSubstringWithPrefix(): void
    {
        $doi = '100002';
        $separator = '/pub/';
        $prefix = 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live';

        $dfu = DatasetFilesUpdater::build(true);
        $success = $dfu->replaceFileUrlSubstringWithPrefix($doi, $separator, $prefix);
        codecept_debug("Number of file URLs updated: " . $success);
        $this->assertTrue($success === 7, "Unexpected number of file URLs updated");
    }
}