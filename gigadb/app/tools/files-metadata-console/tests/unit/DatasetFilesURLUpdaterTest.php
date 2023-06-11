<?php

use app\components\DatasetFilesURLUpdater;

/**
 * Tests DatasetFilesURLUpdater component
 */
class DatasetFilesURLUpdaterTest extends \Codeception\Test\Unit
{
    public DatasetFilesURLUpdater $dfuu;

    /**
     * Test that a list of dataset DOIs can be returned that need their file
     * URLs updating
     */
    public function testGetPendingDatasets(): void
    {
        $batchSize = 3;

        $dfu = DatasetFilesURLUpdater::build(true);
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

        $dfu = DatasetFilesURLUpdater::build(true);
        $dois = $dfu->getNextPendingDatasets($batchSize, $excludedDois);
        codecept_debug($dois);
        $this->assertEquals(3, sizeof($dois), "Unexpected number of DOIs returned");
        $this->assertTrue(in_array("100002", $dois), "DOI 100002 was not found");
        $this->assertFalse(in_array("100003", $dois), "DOI 100003 should not have been returned");
    }

    /**
     * Test file URLs for dataset DOI 100003 can be updated
     */
    public function testUpdateFileLocationsForDataset(): void
    {
        $separator = '/pub/';
        $doi = '100003';

        $this->dfuu = DatasetFilesURLUpdater::build(true);
        $output = $this->dfuu->replaceLocationsForDatasetFiles($doi, $separator);
        $this->assertTrue($output == 6);
    }

    /**
     * Test ftp_site for dataset DOI 100002 can be updated
     */
    public function testUpdateFTPSiteForDataset(): void
    {
        $doi = '100002';

        $this->dfuu = DatasetFilesURLUpdater::build(true);
        $output = $this->dfuu->replaceFTPSiteForDataset($doi);
        $this->assertTrue($output == 1);
    }
}