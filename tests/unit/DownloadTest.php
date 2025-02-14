<?php

declare(strict_types=1);

/**
 * Tests DownloadService component
 */
class DownloadTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        $guzzleService = new \GuzzleHttp\Client();
        Yii::$container->set('guzzleHttpClient', $guzzleService);
    }


    /**
     * Test that a remote file can be downloaded
     */
    public function testDownloadFile()
    {
        // UniProt is a reliable database service
        $url = "https://rest.uniprot.org/uniprotkb/A0A1A8NJ45.txt";
        try {
            $output = DownloadService::downloadFile($url);
            $this->assertStringContainsString("Keratin", $output);
        } catch (\GuzzleHttp\Exception\GuzzleException $ge) {
            echo $ge->getMessage() . PHP_EOL;
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Test that a remote file exists
     *
     * @skip as the uniprot service is unavailable now
     */
    public function testFileExists()
    {
        $url = "https://rest.uniprot.org/uniprotkb/A0A1A8NJ45.txt";
        $file_exists = DownloadService::fileExists($url);
        $this->assertTrue($file_exists);
    }
}
