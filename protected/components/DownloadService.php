<?php

/**
 * Service to provide tokens for password reset functionality
 */
class DownloadService extends yii\base\Component
{
    /**
     * Initializes application component.
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
     */
    public static function downloadFile(string $url)
    {
        $curl = new Curl\Curl();
        // open the file where the request response should be written
        $tempfile = tmpfile();
        echo "Tempfile: ".$tempfile.PHP_EOL;
        $path = stream_get_meta_data($tempfile)['uri']; // eg: /tmp/phpFx0513a
        echo "Path: ".$path.PHP_EOL;
        $file_handle = fopen($path, 'w+');
        // pass it to the curl resource
        $curl->setOpt(CURLOPT_FILE, $file_handle);
        // do any type of request
        $curl->get($url);
        // Check for errors
        if ($curl->error) {
            echo "Error code: ".$curl->error_code.PHP_EOL;
        }
        else {
            echo "Response code: ".$curl->response.PHP_EOL;
        }
        // disable writing to file
        $curl->setOpt(CURLOPT_FILE, null);
        // close the file for writing
        fclose($file_handle);
        $contents = file_get_contents($path);
        fclose($tempfile);
        echo "Contents: ".$contents;
        return $contents;
    }
}
?>