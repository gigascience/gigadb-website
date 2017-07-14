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
        $this->attachBehavior("fs", new LocalFileSystemBehavior() );

        $this->log_setup();

        $local_dir = Yii::app()->preview->temporary_directory;
        $supported_formats = Yii::app()->preview->supported_media_types;
        $this->log("supported formats: " . var_dump($supported_formats));


        $this->log("GeneratePreviewCommand started") ;

        try {
            $this->queue = Yii::app()->beanstalk->getClient();
            $this->queue->connect();
            $this->queue->watch(Yii::app()->preview->preview_job_queue);
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
                        if (isset($location_parts['path'])) {
                            $basename = pathinfo($location_parts['path'], PATHINFO_BASENAME);
                            $filename = pathinfo($location_parts['path'], PATHINFO_FILENAME);
                        }
                        else {
                            throw new Exception("Malformed job message. location data is missing") ;
                        }

                        // Update redis with inprogress status for frontend
                        $location_md5 = md5($location);
                        $cache = array('status' => 'INPROGRESS', 'url' => '') ;
                        if ( false === Yii::app()->redis->executeCommand('SET',array( $location_md5, json_encode($cache) )) ) {
                            throw new Exception("Failed updating KV server with INPROGRESS status");
                        }
                        else {
                            $this->log("Updated KV server with INPROGRESS status");
                        }

                        //creating working directory
                        $preview_dir = md5($location);
                        $this->log( "Creating working directory " . "$local_dir/$preview_dir" . "...");
                        if (!(is_dir("$local_dir/$preview_dir")))
                            mkdir("$local_dir/$preview_dir", 0700);
                        chdir("$local_dir/$preview_dir");

                        // check wether we support the file for preview
                        $local_destination = "$local_dir/$preview_dir/$basename" ;
                        $mime_type = $this->extension_to_mime_type(  pathinfo($local_destination,PATHINFO_EXTENSION) );
                        //in case the file is a gzipped file, we need to check mime type of uncompressed file
                        if ("application/x-gzip" === $mime_type ) {
                            $uncompressed_extension =  pathinfo($filename,PATHINFO_EXTENSION) ;
                            $mime_type = $this->extension_to_mime_type(  $uncompressed_extension );
                        }

                        $preview_path = false;
                        if( in_array($mime_type , $supported_formats) ) {
                            $this->log("supported format ($mime_type), proceeding to downloading source file");

                            //download file by ftp, starting with connecting to the server
                            $connectionString = $this->buildConnectionString();

                            //download the file
                            $download_status = false ;

                            $this->log("Copying " . "/var/ftp".$location_parts['path'] . " to $local_destination");
                            //$download_status = $this->manage_download($connectionString, "$local_destination", $location_parts['path']) ;
                            $copy_status = copy("/var/ftp". $location_parts['path'], "$local_destination");

                            // ftp_close($conn_id);

                            // if (FTP_FAILED === $download_status) {
                            //     if(is_file($local_destination) && filesize("$local_destination") >  0 ) { //partial download, we release the job for future retry
                            //         $temp_job_id = $this->current_job['id'] ; //because we are about to nullify current_job but needs the id
                            //         $this->queue->release($temp_job_id, 10 , 60) ; //release the job, with delay, at lower priority for future retry
                            //         $this->current_job = null ; // so that the exception is picked up by the outer catch block
                            //         throw new Exception("($temp_job_id) Error while downloading " . $location_parts['path'] . " to $local_destination." . PHP_EOL . "The job $temp_job_id has been released for future retry");
                            //     } else {
                            //         throw new Exception("Failed to download " . $location_parts['path'] . " to $local_destination") ;
                            //     }
                            // }

                            //Generating preview file
                            $preview_path = $this->make_preview("$local_destination"); //TODO extract uncompress as separate function

                        }

                        clearstatcache();
                        if (true === is_file($preview_path)) {

                            //extract filename from preview_path as it has already taken care of .gz files special case
                            $remote_filename = pathinfo($preview_path,PATHINFO_FILENAME);
                            //upload generated preview to s3.
                            $preview_url = $this->upload_preview($location, $preview_path, $remote_filename);
                            if (false === $preview_url) {
                                throw new Exception("Failed uploading preview file ");
                            }
                            else {
                                $this->log("preview file uploaded at $preview_url");
                            }
                            //update redis
                            $cache['status'] = "COMPLETED" ;
                            $cache['url'] = $preview_url ;
                            if ( false === Yii::app()->redis->executeCommand('SET',array( $location_md5, json_encode($cache) )) ) {
                                throw new Exception("Failed updating KV server with COMPLETED status");
                            }
                            else {
                                $this->log("Updated KV server with COMPLETED status");
                            }

                            //job done, deleting the job
                            $this->log("this job has been processed successfully" );
                            if ($this->queue->delete($this->current_job['id'])) {
                                $this->log("this job has been deleted from the queue" );
                            }
                            else {
                                $this->log("Error deleting the job from the queue");
                            }

                        } else {

                            //job skipped because unsupported file format
                            $this->log("skipping this job as mime type $mime_type for $local_destination is not supported") ;
                            if ($this->queue->delete($this->current_job['id'])) {
                                $this->log("this job has been deleted from the queue" );
                            }
                            else {
                                $this->log("Error deleting the job from the queue");
                            }
                        }

                    }
                    else {
                        throw new Exception("Failed touching the newly reserved job");
                    }


                    $this->rrmdir("$local_dir/$preview_dir") ;
                    if(false === is_dir("$local_dir/$preview_dir")) {
                        $this->log("temporary directory $local_dir/$preview_dir removed" . PHP_EOL);
                    }
                    else {
                        $this->log("Error removing temporary directory $local_dir/$preview_dir" . PHP_EOL );
                    }
                    $this->current_job = null;

                }catch (Exception $loopex) {
                    $code = $loopex->getCode() ;
                    $message = $loopex->getMessage() ;
                    $this->log($message) ;
                    if ($this->current_job) {
                        if ( E_NOTICE === $code || E_WARNING === $code || E_ERROR === $code ) {
                            $this->queue->release($this->current_job['id'],0,60*2); //release the job if code error
                            $cache['status'] = "DELAYED" ;
                            if ( false === Yii::app()->redis->executeCommand('SET',array( $location_md5, json_encode($cache) )) ) {
                                throw new Exception("Failed updating KV server with DELAYED status");
                            }
                            else {
                                $this->log("Updated KV server with DELAYED status");
                            }
                        }
                        else {
                            $this->queue->bury($this->current_job['id'],0); //bury the job as they are something wrong with the data
                            $cache['status'] = "FAILED" ;
                            if ( false === Yii::app()->redis->executeCommand('SET',array( $location_md5, json_encode($cache) )) ) {
                                throw new Exception("Failed updating KV server with FAILED status");
                            }
                            else {
                                $this->log("Updated KV server with FAILED status");
                            }
                        }
                        $this->log( "Due to errors, this job has been " . $this->queue->statsJob($this->current_job['id'])['state']);
                    }
                    else {
                        throw new Exception ($message, $code) ;
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

    function make_preview($sourcepath) {
        $size_threshold = "200000";
        $type = "inode/x-empty";

        //identify type
        $filetype = mime_content_type("$sourcepath");
        //gunzip first if necessary
        if ("application/x-gzip" === $filetype) {
            $filepath = pathinfo($sourcepath, PATHINFO_DIRNAME) . "/" . pathinfo($sourcepath, PATHINFO_FILENAME);
            $this->ungzip($sourcepath, $filepath);
            $filetype = mime_content_type("$filepath");

            $this->log("Extracted $sourcepath into $filepath" );
        }else {
            $filepath = $sourcepath;
        }
        $filesize = filesize("$filepath") ;

        $this->log("making preview for $filepath of mime type: $filetype and size: $filesize");

        if ("text/plain" === $filetype) {
            if ($filesize > $size_threshold) {
                $preview = "" ;
                $lines = 0 ;
                $this->log("$filepath size > $size_threshold, generating a preview file $filepath.preview" );
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
                $this->log("Truncating $filetype file $filepath to $filepath.preview") ;
                return "$filepath.preview" ;
            }
            else {
                $this->log("Renaming $filetype file $filepath to $filepath.preview") ;
                rename($filepath, "$filepath.preview") ;
                return "$filepath.preview" ;
            }



        }
        else if ("application/xml" === $filetype) {
            $this->log("Renaming $filetype file $filepath to $filepath.xml.preview") ;
            rename($filepath, "$filepath.xml.preview") ;
            return "$filepath.xml.preview" ;
        }
        else {
            $this->log("Renaming file $filepath to $filepath.preview") ;
            rename($filepath, "$filepath.preview") ;
            return "$filepath.preview";
        }

        return "$filepath.preview";
    }

    function upload_preview($location, $preview_path, $basename) {

        //data needed by s3
        $bucket = Yii::app()->preview->preview_bucket;
        $keyname = "$basename";

        $this->log( "Uploading file $preview_path to S3 in bucket:" . $bucket . " with keyname:" . $keyname);

        // Instantiate the S3 client.
        $s3 = Yii::app()->aws->getS3Instance();

        try {
            $result = $s3->putObject(array(
                'Bucket'     => $bucket,
                'Key'        => $keyname,
                'SourceFile' => $preview_path,
                'ACL'        => 'public-read',
                'ContentType' => mime_content_type("$preview_path"),
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
