<?php

use app\components\DatasetFilesUpdater;

/**
 * Tests DatasetFilesUpdater component
 */
class DatasetFilesUpdaterTest extends \Codeception\Test\Unit
{
    public DatasetFilesUpdater $dfu;

    /**
     * Test size of files in dataset 100039 can be updated using
     * information in 100039.filesizes file.
     */
    public function testUpdateFileSizes(): void
    {
        try {
            $dfu = new DatasetFilesUpdater(["doi" => "100039"]);
            $out = $dfu->updateFileSizes();
            codecept_debug($out);
            $this->assertEquals(3, $out, "Unexpected number of files updated");
        }
        catch (Exception $e) {
            codecept_debug($e->getMessage());
        }
    }

    /**
     * Test size of files in dataset 100040 is not updated because
     * there is no 100040.filesizes file in gigadb-datasets-metadata
     * S3 bucket.
     */
    public function testUpdateFileSizesWithNonExistentFile(): void
    {
        try {
            $dfu = new DatasetFilesUpdater(["doi" => "100040"]);
            $out = $dfu->updateFileSizes();
        }
        catch (Exception $e) {
            codecept_debug($e->getMessage());
            $this->assertStringEndsWith('100040.filesizes not found',
                $e->getMessage(),
                'Unexpected exception message');
        }
    }

}