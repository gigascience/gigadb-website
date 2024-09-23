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
     */
    public static function downloadFile(string $url)
    {
        $webClient = Yii::$container->get('guzzleHttpClient');
        $response = $webClient->request('GET', $url, ['http_errors' => false]);

        if ($response->getStatusCode() === 200) {
            return $response->getBody()->getContents();
        } else if ($response->getStatusCode() !== 404) {
            throw new \Exception("Error downloading file by DownloadService: status code " . $response->getStatusCode());
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
            /** @var \GuzzleHttp\Client $webClient */
            $webClient = Yii::$container->get('guzzleHttpClient');
            $response = $webClient->head($url);

            return $response->getStatusCode() === 200;
        } catch (Exception $e) {
            return false;
        }
    }
}
