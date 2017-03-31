<?php

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once dirname(__FILE__). '/../vendors/aws/aws-autoloader.php';
spl_autoload_register(array('YiiBase', 'autoload'));


class UploadBundleCommand extends CConsoleCommand {


    function run($args) {

        try {
            $consumer = Yii::app()->beanstalk->getClient();
            $consumer->connect();
            $consumer->watch('bundleuploading');
            echo "* connected to the job server, waiting for new jobs...\n";

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
                            echo "\n* Job done...deleting (" . $job['id'] . ")\n\n\n";
                            $consumer->delete($job['id']);
                            $this->clean_up("$file_path");
                        }
                    }
                    else {
                        throw new Exception("Failed touching the newly reserved job");
                    }
                }
                catch(Exception $loopex) {
                    echo "Error while processing job of id " . $job['id'] . ":" . $loopex->getMessage();
                    $consumer->bury($job['id'],0);
                    echo "The job of id: " . $job['id'] . " has been " . $consumer->statsJob($job['id'])['state'];
                }
            }
        }
        catch(Exception $topex) {
            echo "Error while initialising the worker: " . $ex->getMessage();
            $consumer->disconnect();
        }

        $consumer->disconnect();

    }

    function process_bundle_upload_job($file_path, $bid) {

        echo "* processing job to upload bundle $bid...\n";
        //data needed by s3
        $bucket = 'gigadb-bundles-test';
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
            echo "Upload complete.\n";
        } catch (MultipartUploadException $e) {
            $uploader->abort();
            echo "Upload failed.\n";
            throw new Exception($e->getMessage());
        }

    }

    function clean_up($file_path) {
        if(is_file($file_path)) {
            unlink($file_path);
        }
    }

}
