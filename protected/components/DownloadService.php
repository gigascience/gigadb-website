<?php

/**
 * Service for downloading remote files on the Web
 */
class DownloadService extends yii\base\Component
{
    /**
     * Initializes application component.
     *
     * This method overrides the parent implementation by setting default cache
     * key prefix.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Downloads and returns contents of a remote file
     *
     * @return string
     * @throws \GuzzleHttp\Exception\BadResponseException
     */
    public static function downloadFile(string $url)
    {
        $webClient = new \GuzzleHttp\Client();
        $response = $webClient->request('GET', $url);
        if ($response->getStatusCode() === 200) {
            return $response->getBody()->getContents();
        } else {
            throw new BadResponseException("Error downloading file by DownloadService: status code " . $response->getStatusCode());
        }
    }

    /**
     * Check remote file exists
     *
     * @param string $url
     * @return boolean
     */
    public static function fileExists(string $url)
    {
        try {
            $webClient = new \GuzzleHttp\Client();
            $webClient->head($url);
            return true;
        } catch (GuzzleHttp\Exception\ClientException $e) {
            echo "No file found at $url" . PHP_EOL;
            return false;
        }
    }
}
