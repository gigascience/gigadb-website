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
        $size_threshold = "200000";


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
                    $this->log("Ready for new jobs...");

                    $this->current_job = $this->queue->reserve();
                    if (false === $this->current_job) {
                        throw new Exception("Error reserving a new job from the job queue: " . PHP_EOL . var_dump($this->queue->statsTube('previewgeneration')) . PHP_EOL . var_dump($this->current_job) );
                    }
                    else {
                        $this->log("About to process new job: " . $this->current_job['id']);
                    }
                    $result = $this->queue->touch($this->current_job['id']);

                    if ($result) {
                        //extract job details
                        $body_array = json_decode($this->current_job['body'], true);
                        $location = $body_array['location'];
                        $location_parts = parse_url($location);
                        $filename = pathinfo($location_parts['path'], PATHINFO_BASENAME);

                        //creating working directory
                        $preview_dir = md5($location);
                        $this->log( "Creating working directory " . "$local_dir/$preview_dir" . "...");
                        if (!(is_dir("$local_dir/$preview_dir")))
                            mkdir("$local_dir/$preview_dir", 0700);
                        chdir("$local_dir/$preview_dir");


                        //download file by ftp, starting with connecting to the server
                        $connectionString = "ftp://anonymous:anonymous@10.1.1.33:21/pub/10.5524"; //TODO
                        $conn_id = $this->getFtpConnection($connectionString);
                        if (false == $conn_id) { //if connection fail, relase the job for future retry as it could be connection issues
                            $temp_job_id = $this->current_job['id'] ; //because we are about to nullify current_job but needs the id
                            $this->queue->release($temp_job_id, 10 , 60) ; //release the job, with delay, at lower priority for future retry
                            $this->current_job = null ; // so that the exception is picked up by the outer catch block
                            throw new Exception("Failed connecting to FTP server, the job $temp_job_id has been released for future retry") ;
                        }
                        else {
                            $this->log( "Connected to ftp server, ready to download file from $location...");
                        }
                        //check wether the file exists locally
                        $local_before_size = is_file("$local_dir/$preview_dir/$filename") ? filesize("$local_dir/$preview_dir/$filename") : 0 ;

                        //download the file
                        $download_status = false ;
                        $ftp_mode = $this->get_ftp_mode($location_parts['path']) ;
                        $local_destination = "$local_dir/$preview_dir/$filename" ;

                        if ( $local_before_size > 0 && FTP_BINARY === $ftp_mode) {
                            $this->log("Resuming download of " . $location_parts['path'] . "to $local_destination at $local_before_size");
                            $download_status = ftp_get($conn_id,
                                "$local_destination",
                                $location_parts['path'],
                                $ftp_mode,
                                $local_before_size
                            );
                        } else {
                            $this->log("Starting download of " . $location_parts['path'] . " to $local_destination") ;
                            $download_status = ftp_get($conn_id,
                                "$local_destination",
                                $location_parts['path'],
                                $ftp_mode
                            );
                        }

                        ftp_close($conn_id);

                        if (false === $download_status) {
                            if(filesize("$local_destination") >  0 ) { //partial download, we release the job for future retry
                                $temp_job_id = $this->current_job['id'] ; //because we are about to nullify current_job but needs the id
                                $this->queue->release($temp_job_id, 10 , 60) ; //release the job, with delay, at lower priority for future retry
                                $this->current_job = null ; // so that the exception is picked up by the outer catch block
                                throw new Exception("($temp_job_id) Error while downloading " . $location_parts['path'] . " to $local_destination." . PHP_EOL . "The job $temp_job_id has been released for future retry");
                            } else {
                                throw new Exception("Failed to download " . $location_parts['path'] . " to $local_destination") ;
                            }
                        }

                        //if too big, make small copy for files, otherwise send file as is
                        $preview_path = false;
                        if (filesize("$local_destination") > $size_threshold) {
                            $preview_path = $this->make_content_previewable("$local_destination");
                        }
                        else {
                            $preview_path = "$local_destination" ;
                        }
                        if (true === is_file($preview_path)) {

                            //upload generated preview to s3.
                            $preview_url = $this->upload_preview($location, $preview_path, $filename);
                            if (false === $preview_url) {
                                throw new Exception("Failed uploading preview file ");
                            }
                            else {
                                $this->log("preview file uploaded at $preview_url");
                            }
                            //update redis
                            $cache_result = Yii::app()->redis->set(md5($location),$preview_url);
                            if (false === $cache_result) {
                                throw new Exception("Failed saving preview_url in Redis");
                            }
                            else {
                                $this->log("preview_url saved in Redis with key:" . md5($location) ." and value:" . Yii::app()->redis->get(md5($location)) );
                            }

                        } else {
                            throw new Exception ("Failed to generate a preview for $local_dir/$preview_dir/$filename ") ;
                        }

                    }
                    else {
                        throw new Exception("Failed touching the newly reserved job");
                    }

                    //job done, deleting the job
                    $this->log("job ". $this->current_job['id'] . " completed successfully" );
                    $this->queue->delete($this->current_job['id']);
                    $this->current_job = null;

                }catch (Exception $loopex) {
                    if ($this->current_job) {
                        $this->log( "Error while processing job of id " . $this->current_job['id'] . ":" . $loopex->getMessage());

                        $this->queue->bury($this->current_job['id'],0); //bury the job as they are something wrong with it
                        $this->log( "The job of id: " . $this->current_job['id'] . " has been " . $this->queue->statsJob($this->current_job['id'])['state']);
                    }
                    else {
                        throw new Exception ($loopex->getMessage()) ;
                    }
                }

            }



        }
        catch (Exception $runex) {
            $this->log( "Fatal Error: " . $runex->getMessage());
            $this->queue->disconnect();
            $this->current_job = null ;
            $this->log( "GeneratePreviewCommand stopping");
            return 1;
        }

        $this->queue->disconnect();
        $this->current_job = null ;
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

        //data needed by s3
        $bucket = Yii::app()->aws->preview_bucket;
        $keyname = "$filename";

        $this->log( "Uploading file $preview_path to S3 in bucket:" . $bucket . " with keyname:" . $keyname);

        // Instantiate the S3 client.
        $s3 = Yii::app()->aws->getS3Instance();

        try {
            $result = $s3->putObject(array(
                'Bucket'     => $bucket,
                'Key'        => $keyname,
                'SourceFile' => $preview_path,
                'ACL'        => 'public-read',
                'Metadata'   => array(
                    'source.location.md5' => md5($location)
                )
            ));

            if ($result) {
                $this->log( "generated preview uploaded to S3");
                return  $s3->getObjectUrl($bucket, $keyname);
            }else {
                $this->log( "Uploading generated preview to S3 is NOT successful");
                return false;
            }
        }
        catch(Exception $s3ex) {
            $this->log( "Upload of generated preview failed: " . $s3ex->getMessage());
            return false;
        }



        return;
    }



}
