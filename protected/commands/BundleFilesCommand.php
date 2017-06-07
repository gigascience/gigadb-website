<?php
ini_set('max_execution_time', 300);

class BundleFilesCommand extends CConsoleCommand {
    public $queue ;
    public $current_job;

    public function run($args) {


        $local_dir = Yii::app()->multidownload->temporary_directory;

        $this->attachBehavior("loggable", new LoggableCommandBehavior() );
        $this->attachBehavior("ftp", new FileTransferBehavior() );
        $this->attachBehavior("fs", new LocalFileSystemBehavior() );

        $this->log_setup();

        $this->log("BundleFilesCommand started") ;

        try {

            $consumer = Yii::app()->beanstalk->getClient();
            $consumer->connect();
            $consumer->watch(Yii::app()->multidownload->multidownload_job_queue);
            $this->log("connected to the job server, waiting for new jobs...") ;

            if (false === is_dir($local_dir) ) {
                $workdir_status = mkdir("$local_dir", 0700);
                if (false === $workdir_status) {
                    throw new Exception ("Error creating directory $local_dir");
                }
            }

            $this->log("work directory created..." ) ;

            while (true) {

                try {

                    $this->log("Ready for new jobs...");

                    $this->current_job = $consumer->reserve();
                    if (false === $this->current_job) {
                        throw new Exception("Error reserving a new job from the job queue");
                    }
                    $result = $consumer->touch($this->current_job['id']);

                    if( $result )
                    {

                        $body_array = json_decode($this->current_job['body'], true);
                        $bundle = unserialize($body_array['list']);
                        $bid = $body_array['bid'];
                        $dataset_id = $body_array['dataset_id'];

                        $this->log("Got a new job...") ;



                        //create directory for the files
                        $bundle_dir = $bid;
                        $this->log("Creating working directory " . "$local_dir/$bundle_dir" . "...") ;
                        if (!(is_dir("$local_dir/$bundle_dir")))
                            mkdir("$local_dir/$bundle_dir", 0700);
                        chdir("$local_dir/$bundle_dir");

                        //create a compressed tar archive
                        $tar = new Archive_Tar("$local_dir/bundle_$bundle_dir.tar.gz", "gz");


                        foreach ($bundle[$dataset_id] as $selection) {
                            $connectionString = $this->buildConnectionString();

                            $location = $selection["location"];
                            $filename = $selection["filename"];
                            $type = $selection["type"];

                            $location_parts = parse_url($location);

                            $this->log("downloading " . $location_parts['path'] . " -> " . "$local_dir/$bundle_dir/$filename " ) ;
                            // $download_status = false;
                            $copy_status = false;
                            // $directory_download_status = false;
                            $directory_copy_status = false;
                            chdir("$local_dir/$bundle_dir/");

                            if ($type === "Directory") {
                                // $directory_download_status = $this->ftp_getdir($connectionString, $location_parts['path'], $dataset_id);
                                $portable_path = str_replace("/pub/10.5524/100001_101000/$dataset_id/","", $location_parts['path']);
                                $directory_copy_status = $this->local_getdir("/var/ftp". $location_parts['path'], "$local_dir/$bundle_dir/$portable_path");
                                if ( $directory_copy_status  ) { //add the directory to the archive
                                    $this->log("adding " . "$portable_path" .  " to $local_dir/bundle_$bundle_dir.tar.gz as $portable_path") ;
                                    $archive_status = $tar->addModify(["$local_dir/$bundle_dir/$portable_path/"], "", "$local_dir/$bundle_dir");
                                    if ($tar->error_object) {
                                        throw new Exception("Error while:" . "adding " . "$local_dir/$bundle_dir/$portable_path" .  " to $local_dir/bundle_$bundle_dir.tar.gz: " . $tar->error_object);
                                    }
                                }
                                else {
                                    $this->log("directory " .  $location_parts['path'] . " couldn't be downloaded") ;
                                }
                            }
                            else {
                                // $download_status = $this->manage_download($connectionString, "$local_dir/$bundle_dir/$filename", $location_parts['path'] );
                                $copy_status = copy("/var/ftp". $location_parts['path'], "$local_dir/$bundle_dir/$filename");

                                if ($copy_status) {
                                    $this->log("Successfully downloaded " . $location_parts['path']) ;
                                    if (pathinfo($location_parts['path'], PATHINFO_DIRNAME) === "/pub/10.5524/100001_101000/$dataset_id") {
                                        $portable_path = "" ;
                                        $this->log("adding " . "$filename" .  " to $local_dir/bundle_$bundle_dir.tar.gz") ;
                                        $archive_status = $tar->addModify(["$local_dir/$bundle_dir/$filename"], $portable_path, "$local_dir/$bundle_dir/");
                                    }
                                    else {
                                        $portable_path = str_replace("/pub/10.5524/100001_101000/$dataset_id/","", pathinfo($location_parts['path'], PATHINFO_DIRNAME));
                                        $this->log("adding " . "$portable_path/$filename" .  " to $local_dir/bundle_$bundle_dir.tar.gz") ;
                                        $archive_status = $tar->addModify(["$local_dir/$bundle_dir/$filename"], $portable_path, "$local_dir/$bundle_dir/");
                                    }
                                    if (false === $archive_status) {
                                        throw new Exception("Error while:" . "adding " . "$local_dir/$bundle_dir/$filename" .  " to $local_dir/bundle_$bundle_dir.tar.gz");
                                    }
                                }
                                else {
                                    $this->log("Failed downloading " . $location_parts['path']) ;
                                }

                            }


                        }
                        $publish_status = $this->local_publish("$local_dir/bundle_$bundle_dir.tar.gz", "/var/ftp" . Yii::app()->multidownload->ftp_bundle_directory . "/bundle_$bundle_dir.tar.gz");
                        if ( true === $publish_status  ) {
                            $cache['status'] = "PUBLISHED" ;
                            Yii::app()->redis->executeCommand('SET',array( $bundle_dir, json_encode($cache) )) ;
                            $this->log("bundle successfuly published");
                        }
                        else {
                            throw new Exception("Failed publishing the bundle");
                        }
                        // $upload_job = $this->prepare_upload_job("$local_dir/bundle_$bundle_dir.tar.gz",$bid);
                        // if($upload_job) {
                        //     $this->log("Submitted an upload job with id: $upload_job") ;
                        // }
                        // else {
                        //     $this->log("An error occured while submitting an upload job") ;
                        // }

                        $this->log("Job done...(" . $this->current_job['id'] . ")") ;
                        $deletion_status = $consumer->delete($this->current_job['id']);
                        if (true === $deletion_status) {
                            $this->log("Job for bundle $bid successfully deleted") ;
                        }
                        else {
                            $this->log("Failed to delete job for bundle $bid]") ;
                        }
                        $this->current_job = null ;
                        $this->rrmdir("$local_dir/$bundle_dir");

                    }
                    else
                    {
                        throw new Exception("Failed touching the newly reserved job");
                    }
                }
                catch(Exception $loopex) {
                    $this->log("Error while processing job of id " . $this->current_job['id'] . ":" . $loopex->getMessage()) ;
                    $consumer->bury($this->current_job['id'],0);
                    $this->log("The job of id: " . $this->current_job['id'] . " has been " . $consumer->statsJob($this->current_job['id'])['state']) ;
                    $this->current_job = null ;
                }


            }

            $consumer->disconnect();
            $this->log("Closed FTP connection and stopped listenging to the job queue...") ;

        } catch (Exception $runex) {
            $this->log("Error while initialising the worker: " . $runex->getMessage()) ;
            ftp_close($conn_id);
            $consumer->disconnect();
            $this->current_job = null ;
            $this->log( "BundleFilesCommand stopping");
            return 1;
        }

        $consumer->disconnect();
        $this->log( "BundleFilesCommand stopping");
        return 0;
    }


    // function prepare_upload_job($file_path, $bid) {
    //     $client = Yii::app()->beanstalk->getClient();
    //     $client->useTube('bundleuploading');
    //     $jobDetails = [
    //         'application'=>'gigadb-website',
    //         'file_path'=>$file_path,
    //         'bid'=>$bid,
    //         'submission_time'=>date("c"),
    //     ];
    //
    //     $jobDetailString = json_encode($jobDetails);
    //
    //     $ret = $client->put(
    //         0, // priority
    //         0, // do not wait, put in immediately
    //         90, // will run within n seconds
    //         $jobDetailString // job body
    //     );
    //
    //     return $ret;
    //
    // }


}
