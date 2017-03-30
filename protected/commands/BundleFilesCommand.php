<?php


class BundleFilesCommand extends CConsoleCommand {

    public function run($args) {

        $queue = "bundle_queue";
        $local_dir = "/tmp/bundles";

        try {

            $consumer = Yii::app()->beanstalk->getClient();
            $consumer->connect();
            $consumer->watch('filespackaging');
            echo "* connected to the job server, waiting for new jobs...\n";

            if (false === is_dir($local_dir) ) {
                $workdir_status = mkdir("$local_dir", 0700);
                if (false === $workdir_status) {
                    throw new Exception ("Error creating directory $local_dir");
                }
            }

            echo "\n* work directory created...\n" ;

            while (true) {

                try {

                    echo "Reserving next job...\n";

                    $job = $consumer->reserve();
                    if (false === $job) {
                        throw new Exception("Error reserving a new job from the job queue");
                    }
                    $result = $consumer->touch($job['id']);

                    if( $result )
                    {

                        $body_array = json_decode($job['body'], true);
                        $bundle = unserialize($body_array['list']);
                        $bid = $body_array['bid'];

                        echo "* Got a new job...\n";

                        $connectionString = "ftp://anonymous:anonymous@10.1.1.33:21/pub/10.5524";
                        $conn_id = $this->getFtpConnection($connectionString);

                        echo "\n* connected to ftp server, ready to download files...\n";

                        //create directory for the files
                        $bundle_dir = $bid;
                        mkdir("$local_dir/$bundle_dir", 0700);

                        //create a compressed tar archive
                        $tar = new Archive_Tar("$local_dir/bundle_$bundle_dir.tar.gz", "gz");


                        foreach ($bundle as $location => $filename) {
                            $location_parts = parse_url($location);

                            echo "* downloading " . $location_parts['path'] . " -> " . "$local_dir/$bundle_dir/$filename \n" ;
                            $download_status = false;
                            $download_status = ftp_get($conn_id,
                            "$local_dir/$bundle_dir/$filename",
                            $location_parts['path'],
                            $this->get_ftp_mode($location_parts['path'])
                            );

                            if (false === $download_status) {
                                echo "* Error while downloading" .  $location_parts['path'] . "\n" ;
                                $fp = fopen("$local_dir/$bundle_dir/$filename.error", 'w');
                                fwrite($fp, "Error while downloading from " . $location_parts['path']. ": \n");
                                fwrite($fp, error_get_last()['message']);
                                fclose($fp);
                                $archive_status = $tar->add(["$local_dir/$bundle_dir/$filename.error"]);
                            }
                            else {
                                echo "* adding " . "$local_dir/$bundle_dir/$filename" .  " to $local_dir/bundle_$bundle_dir.tar.gz\n";
                                $archive_status = $tar->addModify(["$local_dir/$bundle_dir/$filename"], "", $local_dir);

                                if (false === $archive_status) {
                                    throw new Exception("Error while:" . "adding " . "$local_dir/$bundle_dir/$filename" .  " to $local_dir/bundle_$bundle_dir.tar.gz\n");
                                }
                            }
                        }

                        echo "\n* Job done...\n\n\n";
                        $deletion_status = $consumer->delete($job['id']);
                        if (true === $deletion_status) {
                            echo "Job for bundle $bid successfully deleted\n";
                        }
                        else {
                            echo "Failed to delete job for bundle $bid]\n";
                        }

                        $this->clean_up("$local_dir/$bundle_dir");
                        $this->prepare_upload_job("$local_dir/bundle_$bundle_dir.tar.gz",$bid);
                        ftp_close($conn_id);
                    }
                    else
                    {
                        throw new Exception("Failed touching the newly reserved job");
                    }
                }
                catch(Exception $loopex) {
                    ftp_raw($conn_id, 'NOOP');
                    echo "Error while processing job of id " . $job['id'] . ":" . $loopex->getMessage();
                    $consumer->bury($job['id'],0);
                    echo "The job of id: " . $job['id'] . " has been " . $consumer->statsJob($job['id'])['state'];

                }




            }

            ftp_close($conn_id);
            $consumer->disconnect();
            echo "* Closed FTP connection and stopped listenging to the job queue...\n";

        } catch (Exception $ex) {
            echo "Error while initialising the worker: " . $ex->getMessage();
            ftp_close($conn_id);
            $consumer->disconnect();
        }
    }


    function prepare_upload_job($file_path, $bid) {
        $client = Yii::app()->beanstalk->getClient();
        $client->useTube('bundleuploading');
        $jobDetails = [
            'application'=>'gigadb-website',
            'file_path'=>$file_path,
            'bid'=>$bid,
            'submission_time'=>date("c"),
        ];

        $jobDetailString = json_encode($jobDetails);

        $ret = $client->put(
            0, // priority
            0, // do not wait, put in immediately
            90, // will run within n seconds
            $jobDetailString // job body
        );

        if ($ret) {
            return $bid; //return the bundle id that identifies the bundle across all systems
        }
        else {
            return false;
        }

    }

    function clean_up($directory) {
        $files = array_diff(scandir($directory), array('..', '.'));
        foreach ($files as $file) {
            unlink("$directory/$file");
        }
        $rmdir_status  = rmdir($directory);
        if (false === $rmdir_status) {
            echo "Failed removing $directory";
        }

    }

    function getFtpConnection($uri)
    {
        // Split FTP URI into:
        // $match[0] = ftp://username:password@sld.domain.tld:port/path1/path2/
        // $match[1] = username
        // $match[2] = password
        // $match[3] = sld.domain.tld
        // $match[4] = port
        // $match[5] = /path1/path2/
        preg_match("/ftp:\/\/(.*?):(.*?)@(.*?):(\d+)(\/.*)/i", $uri, $match);

        // Set up a connection

        $conn = ftp_connect($match[3], $match[4], 120);
        if (false === $conn) {
            throw new Exception("Cannot connect with ftp_connect($match[3], $match[4], 120)");
        }

        // Login
        if (ftp_login($conn, $match[1], $match[2]))
        {
            // Change the dir
            if($match[5]) {
                ftp_chdir($conn, $match[5]);
            }

            //set PASV mode
            ftp_pasv($conn, true);

            // Return the resource
            return $conn;
        }
        else {
            throw new Exception("Cannot login to ftp server with ftp_login($conn, $match[1], $match[2])");
        }

        // Or retun null
        return null;
    }

    function get_ftp_mode($filepath)
    {
        $path_parts = pathinfo($filepath);

        if (!isset($path_parts['extension'])) return FTP_BINARY;
        switch (strtolower($path_parts['extension'])) {
            case 'am':case 'asp':case 'bat':case 'c':case 'cfm':case 'cgi':case 'conf':
            case 'cpp':case 'css':case 'dhtml':case 'diz':case 'h':case 'hpp':case 'htm':
            case 'html':case 'in':case 'inc':case 'js':case 'm4':case 'mak':case 'nfs':
            case 'nsi':case 'pas':case 'patch':case 'php':case 'php3':case 'php4':case 'php5':
            case 'phtml':case 'pl':case 'po':case 'py':case 'qmail':case 'sh':case 'shtml':
            case 'sql':case 'tcl':case 'tpl':case 'txt':case 'vbs':case 'xml':case 'xrc':
            case 'tsv':case 'fastq':case 'fq':case 'fasta':case 'fna':case 'ffn':case 'faa':
            case 'frn':case 'raw':case 'fq':case 'mzml':case 'mzxml':case 'csv':
                return FTP_ASCII;
        }
        return FTP_BINARY;
    }


}
