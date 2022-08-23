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
     * @throws Exception|\GuzzleHttp\Exception\GuzzleException
     */
    public static function downloadFile(string $url)
    {
        $webClient = new \GuzzleHttp\Client();
        $response = $webClient->request('GET', $url);
        if ($response->getStatusCode() === 200)
            return $response->getBody()->getContents();
        else
            throw new Exception("Error downloading file by DownloadService: status code " . $response->getStatusCode());
    }
}
?>