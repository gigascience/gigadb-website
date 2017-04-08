<?php

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once dirname(__FILE__). '/../vendors/aws/aws-autoloader.php';
spl_autoload_register(array('YiiBase', 'autoload'));


class GeneratePreviewCommand extends CConsoleCommand {

    public $queue ;
    public $current_job;

    public function run($args) {


        $this->attachBehavior("loggable", new LoggableCommandBehavior() );
        $this->attachBehavior("ftp", new FileTransferBehavior() );

        $local_dir = "/tmp/previews";
        $threshold = "200000";


        $this->log("GeneratePreviewCommand started") ;
        // $this->log("mime for csv: " . mime_content_type("/vagrant/consortium_list.csv")) ;
        // $this->log("mime for tsv: " . mime_content_type("/vagrant/full-map.tsv")) ;
        // $this->log("mime for fa.gz: " . mime_content_type("/vagrant/wisent.fa.gz")) ;
        // $this->log("mime for fa: " . mime_content_type("/vagrant/V_corymbosum_scaffold_May_2013.fa")) ;

        try {
            $this->queue = Yii::app()->beanstalk->getClient();
            $this->queue->connect();
            $this->queue->watch('previewgeneration');
            $this->log( "connected to the job server, waiting for new jobs...");

            if (false === is_dir($local_dir) ) {
                $workdir_status = mkdir("$local_dir", 0700);
                if (false === $workdir_status) {
                    throw new Exception ("Error creating directory $local_dir");
                }
                $this->log( "work directory created..." ) ;
            }
            else {
                $this->log( "work directory already present..." ) ;
            }

            while(true) {
                try {

                    $job = $this->queue->reserve();
                    if (false === $job) {
                        throw new Exception("Error reserving a new job from the job queue");
                    }
                    else {
                        $this->current_job = $job;
                    }
                    $result = $this->queue->touch($job['id']);

                    if ($result) {

                        //creating working directory
                        $preview_dir = self::random_string(20);
                        $this->log( "Creating working directory " . "$local_dir/$preview_dir" . "...");
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
                        $this->log( "connected to ftp server, ready to download file from $location...");
                        $download_status = ftp_get($conn_id,
                            "$local_dir/$preview_dir/$filename",
                            $location_parts['path'],
                            $this->get_ftp_mode($location_parts['path'])
                        );
                        if (false === $download_status) {
                            throw new Exception("error downloading " . $location_parts['path'] . " to $local_dir/$preview_dir/$filename") ;
                        }
                        ftp_close($conn_id);
                        //if too big, make small copy for files, otherwise send file as is
                        $preview_path = false;
                        if (filesize("$local_dir/$preview_dir/$filename") > 200000) {
                            $preview_path = $this->make_content_previewable("$local_dir/$preview_dir/$filename");
                        }
                        else {
                            $preview_path = "$local_dir/$preview_dir/$filename" ;
                        }
                        if (true === is_file($preview_path)) {

                            //upload generated preview to s3.
                            $preview_url = $this->upload_preview($location, $preview_path, $filename);
                            if (false === $preview_url) {
                                throw new Exception("Failed uploading preview file ");
                            }
                            //update redis
                            $cache_result = Yii::app()->redis->set(md5($location),$preview_url);
                            if (false === $cache_result) {
                                throw new Exception("Failed saving preview_url in Redis");
                            }

                        } else {
                            throw new Exception ("Failed to generate a preview for $local_dir/$preview_dir/$filename ") ;
                        }
                    }
                    else {
                        throw new Exception("Failed touching the newly reserved job");
                    }

                }catch (Exception $loopex) {
                    $this->log( "Error while processing job of id " . $job['id'] . ":" . $loopex->getMessage());
                    $this->queue->bury($job['id'],0);
                    $this->log( "The job of id: " . $job['id'] . " has been " . $this->queue->statsJob($job['id'])['state']);
                }
            }



        }
        catch (Exception $runex) {
            $this->log( "Error while initialising the worker: " . $ex->getMessage());
            $this->queue->disconnect();
            $this->log( "GeneratePreviewCommand stopping");
            return 1;
        }

        $this->queue->disconnect();
        $this->log( "GeneratePreviewCommand stopping");
        return 0;

    }

    function make_content_previewable($sourcepath) {
        $type = "inode/x-empty";
        $preview = "" ;
        $lines = 0 ;


        //identify type
        $filetype = mime_content_type("$sourcepath");
        //gunzip first if necessary
        if ("application/x-gzip" === $filetype) {
            $zip = new ZipArchive;
            $res = $zip->open($sourcepath);
            if (true === $res) {
              $zip->extractTo( pathinfo($sourcepath, PATHINFO_DIRNAME) );
              $zip->close();
              $filepath = pathinfo($sourcepath, PATHINFO_DIRNAME) . "/" . pathinfo($sourcepath, PATHINFO_FILENAME);
              $filetype = mime_content_type("$filepath");
            } else {
                throw new Exception ("Problem unziping $sourcepath");
            }
        }else {
            $filepath = $sourcepath;
        }


        //only support text/plain for now
        if ("text/plain" === $filetype) {
            $handle = fopen("$filepath", "r");
            if (false === $handle ) {
                throw new Exception("Couldn't get handle for $filepath");
            }
            else {
                while (!feof($handle)) {
                    if ($lines > 30) {
                        break;
                    }
                    $buffer = fgets($handle, 4096);
                    $preview .= $buffer.PHP_EOL;
                    $lines++;
                }
                fclose($handle);
            }

            $fp = fopen("$filepath.preview", 'w');
            fwrite($fp, $preview);
            fclose($fp);
            return "$filepath.preview" ;
        }

        return false;
    }

    function upload_preview($location, $preview_path, $filename) {

        $this->log( "Uploading generated preview to S3...");
        //data needed by s3
        $bucket = Yii::app()->aws->bundle_bucket;
        $keyname = "$filename";

        // Instantiate the S3 client.
        $s3 = Yii::app()->aws->getS3Instance();

        try {
            $result = $client->putObject(array(
                'Bucket'     => $bucket,
                'Key'        => $keyname,
                'SourceFile' => pathinfo($preview_path,PATHINFO_DIRNAME),
                'ACL'        => 'public-read',
                'Metadata'   => array(
                    'source.location.md5' => md5($location)
                )
            ));

            if ($result) {
                $this->log( "generated preview uploaded to S3");
                return  $client->getObjectUrl($bucket, $keyname);
            }else {
                $this->log( "Uploading generated preview to S3 is NOT successful");
                return false;
            }
        }
        catch(Exception $s3ex) {
            $this->log( "Upload of generated preview failed: " . $e->getMessage());
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
