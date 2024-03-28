<?php

use app\components\DatasetFilesUpdater;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;

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
        $webClient = new Client([ 'allow_redirects' => false ]);
        $us = new URLsService();
        $dfu = new DatasetFilesUpdater(["doi" => "100039", "us" => $us, "webClient" => $webClient]);
        $out = $dfu->updateFileSizes();
        codecept_debug($out);
        $this->assertEquals(3, $out, "Unexpected number of files updated");
    }

    /**
     * Test size of files in dataset 100039 can be updated using
     * information in 100039.filesizes file.
     */
    public function testUpdateFileSizesWithNonExistentFile(): void
    {
        $webClient = new Client([ 'allow_redirects' => false ]);
        $us = new URLsService();
        try {
            $dfu = new DatasetFilesUpdater(["doi" => "100040", "us" => $us, "webClient" => $webClient]);
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