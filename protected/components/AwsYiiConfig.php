<?php

class AwsYiiConfig extends CApplicationComponent
{

    private $_s3;
    public $access_key;
    public $secret_key;
    public $aws_region;
    public $s3_bucket;


    public function init() {
        parent::init();

        spl_autoload_unregister(array('YiiBase', 'autoload'));
        require_once 'vendors/aws/aws-autoloader.php';
        spl_autoload_register(array('YiiBase', 'autoload'));
    }

    private function initialiazeS3Client() {

        $this->_s3 = \Aws\S3\S3Client::factory(array(
            'key'    => $this->access_key,
            'secret' => $this->secret_key,
        ));
    }

    public function getS3Instance() {
        if ($this->_s3 === NULL) $this->initialiazeS3Client();
        return $this->_s3;
    }

}
 ?>
