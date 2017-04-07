<?php

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once dirname(__FILE__). '/../vendors/aws/aws-autoloader.php';
spl_autoload_register(array('YiiBase', 'autoload'));


class GeneratePreviewCommand extends CConsoleCommand {


    public function run($args) {

        $local_dir = "/tmp/previews";
        $threshold = "200000";

        $this->attachBehavior("loggable", new LoggableCommandBehavior() );
        $this->attachBehavior("ftp", new FileTransferBehavior() );

        $this->log("GeneratePreviewCommand started", pathinfo(__FILE__, PATHINFO_FILENAME)) ;

        try {
            $consumer = Yii::app()->beanstalk->getClient();
            $consumer->connect();
            $consumer->watch('previewgeneration');
            $this->log( "connected to the job server, waiting for new jobs...", pathinfo(__FILE__, PATHINFO_FILENAME));

            if (false === is_dir($local_dir) ) {
                $workdir_status = mkdir("$local_dir", 0700);
                if (false === $workdir_status) {
                    throw new Exception ("Error creating directory $local_dir");
                }
                $this->log( "work directory created..." , pathinfo(__FILE__, PATHINFO_FILENAME)) ;
            }
            else {
                $this->log( "work directory already present..." , pathinfo(__FILE__, PATHINFO_FILENAME)) ;
            }

            while(true) {
                try {

                    $job = $consumer->reserve();
                    if (false === $job) {
                        throw new Exception("Error reserving a new job from the job queue");
                    }
                    $result = $consumer->touch($job['id']);

                    if ($result) {

                        //creating working directory
                        $preview_dir = $self::random_string(20);
                        $this->log( "Creating working directory " . "$local_dir/$preview_dir" . "...", pathinfo(__FILE__, PATHINFO_FILENAME));
                        if (!(is_dir("$local_dir/$preview_dir")))
                            mkdir("$local_dir/$preview_dir", 0700);
                        chdir("$local_dir/$preview_dir");

                        //extract job details
                        $body_array = json_decode($job['body'], true);
                        $location = $body_array['location'];
                        $location_parts = parse_url($location);
                        $filename = pathinfo($location_parts['path'], PATHINFO_BASENAME);
                        //download file
                        $connectionString = "ftp://anonymous:anonymous@10.1.1.33:21/pub/10.5524";
                        $conn_id = $this->getFtpConnection($connectionString);
                        $this->log( "connected to ftp server, ready to download file from $location...", pathinfo(__FILE__, PATHINFO_FILENAME));
                        $download_status = ftp_get($conn_id,
                            "$local_dir/$preview_dir/$filename",
                            $location_parts['path'],
                            $this->get_ftp_mode($location_parts['path'])
                        );
                        ftp_close($conn_id);
                        //if too big, make small copy for files
                        //upload generated preview to s3.
                        $preview_url = $this->upload_preview($location, $local_dir, $preview_dir, $filename);
                        //update redis
                        $cache_result = Yii::app()->redis->set(md5($location),$preview_url);
                        if (false === $cache_result) {
                            throw new Exception("Failed saving preview_url in Redis");
                        }
                    }
                    else {
                        throw new Exception("Failed touching the newly reserved job");
                    }

                }catch (Exception $loopex) {
                    $this->log( "Error while processing job of id " . $job['id'] . ":" . $loopex->getMessage(), pathinfo(__FILE__, PATHINFO_FILENAME));
                    $consumer->bury($job['id'],0);
                    $this->log( "The job of id: " . $job['id'] . " has been " . $consumer->statsJob($job['id'])['state'], pathinfo(__FILE__, PATHINFO_FILENAME));
                }
            }



        }
        catch (Exception $runex) {
            $this->log( "Error while initialising the worker: " . $ex->getMessage(), pathinfo(__FILE__, PATHINFO_FILENAME));
            $consumer->disconnect();
            $this->log( "GeneratePreviewCommand stopping", pathinfo(__FILE__, PATHINFO_FILENAME));
            return 1;
        }

        $consumer->disconnect();
        $this->log( "GeneratePreviewCommand stopping", pathinfo(__FILE__, PATHINFO_FILENAME));
        return 0;

    }


    function upload_preview($location, $local_dir, $preview_dir, $filename) {

        $this->log( "Uploading generated preview to S3...", pathinfo(__FILE__, PATHINFO_FILENAME));
        //data needed by s3
        $bucket = Yii::app()->aws->bundle_bucket;
        $keyname = "$filename";

        // Instantiate the S3 client.
        $s3 = Yii::app()->aws->getS3Instance();

        try {
            $result = $client->putObject(array(
                'Bucket'     => $bucket,
                'Key'        => $keyname,
                'SourceFile' => "$local_dir/$preview_dir/$filename",
                'ACL'        => 'public-read',
                'Metadata'   => array(
                    'source.location.md5' => md5($location)
                )
            ));

            if ($result) {
                $this->log( "generated preview uploaded to S3", pathinfo(__FILE__, PATHINFO_FILENAME));
                return  $client->getObjectUrl($bucket, $keyname);
            }else {
                $this->log( "Uploading generated preview to S3 is NOT successful", pathinfo(__FILE__, PATHINFO_FILENAME));
                return false;
            }
        }
        catch(Exception $s3ex) {
            $this->log( "Upload of generated preview failed: " . $e->getMessage(), pathinfo(__FILE__, PATHINFO_FILENAME));
            return false;
        }



        return;
    }



    private static function random_string($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

}
