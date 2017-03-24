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
        $local_dir = "/tmp";

        $consumer = Yii::app()->beanstalk->getClient();
        $consumer->connect();
        $consumer->watch('filespackaging');
        echo "* connected to the job server, waiting for new jobs...\n";

        $connectionString = "ftp://anonymous:anonymous@10.1.1.33:21/pub/10.5524";


        $conn_id = $this->getFtpConnection($connectionString);
        ftp_pasv($conn_id, true);

        echo "* connected to ftp server, waiting for new file downloads...\n";

        try {

            while (true) {

                ftp_raw($conn_id, 'NOOP');
                $job = $consumer->reserve();
                $result = $consumer->touch($job['id']);

                if( $result )
                {
                    $body_array = json_decode($job['body'], true);
                    $bundle = unserialize($body_array['list']);
                    echo "* Got a new job:\n";
                    echo var_dump($bundle);

                    //create directory for the files
                    $bundle_dir = self::random_string(20);
                    mkdir("$local_dir/$bundle_dir", 0700);

                    //create a compressed tar archive
                    $tar = new Archive_Tar("$local_dir/bundle_$bundle_dir.tar.gz", "gz");
                    echo var_dump(ftp_rawlist($conn_id, ".", true) );


                    foreach ($bundle as $file => $file_name) {
                        //$full_local_path = $gftp->get($file," $local_dir/$bundle_dir/$file_name");
                    }

                    echo "* Job done...\n";
                    //$consumer->delete($job['id']);
                    $consumer->release($job['id'],0,5);
                    echo var_dump($consumer->statsJob($job['id']));
                }
                else
                {
                    // handle failure here
                    echo "Burying...\n";
                    $consumer->bury($job['id'],0);
                    echo $consumer->statsJob($job['id']);
                    ftp_close($conn_id);
                    $consumer->disconnect();
                }



            }

            ftp_close($conn_id);
            $consumer->disconnect();

        } catch (Exception $ex) {
            $error = $ex->getMessage();
            echo $error;
            // handle failure here
            echo "Burying...\n";
            $consumer->bury($job['id'],0);
            echo var_dump($consumer->statsJob($job['id']));
            ftp_close($conn_id);
            $consumer->disconnect();
        }
    }


    public static function random_string($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
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


}
