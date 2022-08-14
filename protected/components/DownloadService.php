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
        $curl = new Curl\Curl();
        // open the file where the request response should be written
        $tempfile = tmpfile();
        $path = stream_get_meta_data($tempfile)['uri']; // eg: /tmp/phpFx0513a
        $file_handle = fopen($path, 'w+');
        // pass it to the curl resource
        $curl->setOpt(CURLOPT_FILE, $file_handle);
        // do any type of request
        $curl->get($url);
        if ($curl->error)
            throw new Exception("Error downloading file: code $curl->error_code");

        // Disable writing to file and tidy up
        $curl->setOpt(CURLOPT_FILE, null);
        // close the file for writing
        fclose($file_handle);
        $contents = file_get_contents($path);
        fclose($tempfile);
        return $contents;
    }
}
?>