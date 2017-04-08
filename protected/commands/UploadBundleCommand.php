<?php

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once dirname(__FILE__). '/../vendors/aws/aws-autoloader.php';
spl_autoload_register(array('YiiBase', 'autoload'));


class UploadBundleCommand extends CConsoleCommand {


    function run($args) {


        $this->attachBehavior("loggable", new LoggableCommandBehavior() );

        $this->log("UploadBundleCommand started") ;

        try {
            $consumer = Yii::app()->beanstalk->getClient();
            $consumer->connect();
            $consumer->watch('bundleuploading');

            $this->log( "connected to the job server, waiting for new jobs..." );

            while(true) {
                try {
                    $job = $consumer->reserve();
                    if (false === $job) {
                        throw new Exception("Error reserving a new job from the job queue");
                    }
                    $result = $consumer->touch($job['id']);

                    if( $result )
                    {
                        $body_array = json_decode($job['body'], true);
                        $file_path = $body_array['file_path'];
                        $bid = $body_array['bid'];
                        $job_result = $this->process_bundle_upload_job($file_path, $bid);
                        if(false === $job_result) {
                            throw new Exception(error_get_last()['message']);
                        }
                        else {
                            $this->log( "Job done...deleting (" . $job['id'] . ")");
                            $consumer->delete($job['id']);
                            $this->clean_up("$file_path");
                        }
                    }
                    else {
                        throw new Exception("Failed touching the newly reserved job");
                    }
                }
                catch(Exception $loopex) {
                    $this->log( "Error with job " . $job['id'] . ":" . $loopex->getMessage());
                    $consumer->bury($job['id'],0);
                    $this->log( "The job of id: " . $job['id'] . " has been " . $consumer->statsJob($job['id'])['state']);
                }
            }
        }
        catch(Exception $topex) {
            $this->log( "Error while initialising the worker: " . $ex->getMessage());
            $consumer->disconnect();
            $this->log( "UploadBundleCommand stopping");
            return 1;
        }

        $consumer->disconnect();
        $this->log( "UploadBundleCommand stopping");
        return 0;

    }

    function process_bundle_upload_job($file_path, $bid) {

        $this->log( "Processing job to upload bundle $bid...");
        //data needed by s3
        $bucket = Yii::app()->aws->bundle_bucket;
        $keyname = "$bid.tar.gz";

        // Instantiate the S3 client.
        $s3 = Yii::app()->aws->getS3Instance();

        // Prepare the upload parameters.
        $uploader = \Aws\S3\Model\MultipartUpload\UploadBuilder::newInstance()
        ->setClient($s3)
        ->setSource("$file_path")
        ->setBucket($bucket)
        ->setKey($keyname)
        ->setMinPartSize(25 * 1024 * 1024)
        ->setOption('ACL', 'public-read')
        ->setConcurrency(3)
        ->build();

        // Perform the upload. Abort the upload if something goes wrong.
        try {
            $uploader->upload();
            $this->log( "Upload complete.");
        } catch (MultipartUploadException $e) {
            $uploader->abort();
            $this->log( "Upload failed.");
            throw new Exception($e->getMessage());
        }

    }

    function clean_up($file_path) {
        if(is_file($file_path)) {
            unlink($file_path);
        }
    }

}
