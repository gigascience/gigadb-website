<?php


class BundleFilesCommand extends CConsoleCommand {

    public function run($args) {


        set_error_handler( array($this, "custom_error_handler"));
        $this->process_data();
        return 0;
    }

    private function process_data()
    {
        $queue = "bundle_queue";
        $local_dir = "/tmp/bundles";


        try {

            $consumer = Yii::app()->beanstalk->getClient();
            $consumer->connect();
            $consumer->watch('filespackaging');
            echo "* connected to the job server, waiting for new jobs...\n";

            $connectionString = "ftp://anonymous:anonymous@10.1.1.33:21/pub/10.5524";


            $conn_id = $this->getFtpConnection($connectionString);

            echo "\n* connected to ftp server, waiting for new file downloads...\n";

            if (false === is_dir($local_dir) ) {
                $workdir_status = mkdir("$local_dir", 0700);
                if (false === $workdir_status) {
                    throw new Exception ("Error creating directory $local_dir");
                }
            }

            echo "\n* work directory created...\n" ;

            while (true) {

                try {

                    ftp_raw($conn_id, 'NOOP');
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
                                $archive_status = $tar->add(["$local_dir/$bundle_dir/$filename"]);

                                if (false === $archive_status) {
                                    throw new Exception("Error while:" . "adding " . "$local_dir/$bundle_dir/$filename" .  " to $local_dir/bundle_$bundle_dir.tar.gz\n");
                                }
                            }
                        }

                        echo "\n* Job done...\n\n\n";
                        $consumer->delete($job['id']);
                        $this->clean_up("$local_dir/$bundle_dir");
                        ftp_raw($conn_id, 'NOOP');
                    }
                    else
                    {
                        throw new Exception("Failed touching the newly reserved job");
                    }
                }
                catch(Exception $ex) {
                    ftp_raw($conn_id, 'NOOP');
                    echo "Error while processing job of id " . $job['id'] . ":" . $ex->getMessage();
                    $consumer->bury($job['id'],0);
                    echo "The job of id: " . $job['id'] . " has been " . $consumer->statsJob($job['id'])['state'];

                }




            }

            ftp_close($conn_id);
            $consumer->disconnect();

        } catch (Exception $ex) {
            echo "Error while initialising the worker: " . $ex->getMessage();
            ftp_close($conn_id);
            $consumer->disconnect();
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

    function custom_error_handler($errno, $errstr, $errfile, $errline) {
        // Determine if this error is one of the enabled ones in php config (php.ini, .htaccess, etc)
        $error_is_enabled = (bool)($errno & ini_get('error_reporting') );

        // -- FATAL ERROR
        // throw an Error Exception, to be handled by whatever Exception handling logic is available in this context
        if( in_array($errno, array(E_USER_ERROR, E_RECOVERABLE_ERROR)) && $error_is_enabled ) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }

        // -- NON-FATAL ERROR/WARNING/NOTICE
        // Log the error if it's enabled, otherwise just ignore it
        else if( $error_is_enabled ) {
            error_log( $errstr, 0 );
            echo $errstr;
            return false; // Make sure this ends up in $php_errormsg, if appropriate
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
