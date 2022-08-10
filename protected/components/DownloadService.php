<?php

/**
 * Service to provide tokens for password reset functionality
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
     */
    public static function downloadFile(string $url)
    {
        // Open a file where request response should be written
        $tempfile = tmpfile();
        $path = stream_get_meta_data($tempfile)['uri'];
        // Create handle and pass to curl resource
        $file_handle = fopen($path, 'w+');
        $curl = new Curl\Curl();
        $curl->setOpt(CURLOPT_FILE, $file_handle);
        // Execute request
        $curl->get($url);
        if ($curl->error)
            throw new Exception("Error downloading file: code $curl->error_code");

        // Disable writing to file and tidy up
        $curl->setOpt(CURLOPT_FILE, null);
        fclose($file_handle);
        fclose($tempfile);
        
        return file_get_contents($path);
    }
}
?>