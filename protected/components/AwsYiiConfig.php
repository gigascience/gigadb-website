<?php

class AwsYiiConfig extends CApplicationComponent
{

    private $_s3;
    public $access_key;
    public $secret_key;
    public $bundle_bucket;
    public $preview_bucket;


    private function initializeS3Client() {

        if ( $this->access_key === NULL || $this->secret_key === NULL )	throw new CException('S3 credentials are not set.');

        $this->_s3 = \Aws\S3\S3Client::factory(array(
            'key'    => $this->access_key,
            'secret' => $this->secret_key,
        ));
    }

    public function getS3Instance() {
        if ($this->_s3 === NULL) $this->initializeS3Client();
        return $this->_s3;
    }

}
 ?>
