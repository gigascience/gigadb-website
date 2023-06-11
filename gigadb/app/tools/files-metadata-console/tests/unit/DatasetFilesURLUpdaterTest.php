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
     * Test file URLs for dataset DOI 100002 can be updated
     */
    public function testReplaceFileLocationURLSubstring(): void
    {
        $separator = '/pub/';
        $testLocation1 = 'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100039/readme.txt';
        $newFileLocation1 = 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100039/readme.txt';

        $testLocation2 = 'https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100001/2011vs2001_v2.xls';
        $newFileLocation2 = 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100001/2011vs2001_v2.xls';

        $testLocation3 = 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100001/2011vs2001_v2.xls';

        $this->dfuu = DatasetFilesURLUpdater::build(true);
        $output = $this->dfuu->replaceFileLocationPrefix($testLocation1, $separator);
        codecept_debug($output);
        $this->assertStringContainsString($newFileLocation1, $output);

        $output = $this->dfuu->replaceFileLocationPrefix($testLocation2, $separator);
        codecept_debug($output);
        $this->assertStringContainsString($newFileLocation2, $output);
    }
}