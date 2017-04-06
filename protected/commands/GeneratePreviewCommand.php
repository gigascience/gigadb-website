<?php

class GeneratePreviewCommand extends CConsoleCommand {


    public function run($args) {

        $local_dir = "/tmp/previews";
        $threshold = "200000";

        try {
            $consumer = Yii::app()->beanstalk->getClient();
            $consumer->connect();
            $consumer->watch('previewgeneration');
            echo "* connected to the job server, waiting for new jobs...\n";

            if (false === is_dir($local_dir) ) {
                $workdir_status = mkdir("$local_dir", 0700);
                if (false === $workdir_status) {
                    throw new Exception ("Error creating directory $local_dir");
                }
                echo "\n* work directory created...\n" ;
            }
            else {
                echo "\n* work directory already present...\n" ;
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
                        echo "* Creating working directory " . "$local_dir/$preview_dir" . "...\n";
                        if (!(is_dir("$local_dir/$preview_dir")))
                            mkdir("$local_dir/$preview_dir", 0700);
                        chdir("$local_dir/$preview_dir");

                        //extract job details
                        $body_array = json_decode($job['body'], true);
                        $location = $body_array['location'];
                        $location_parts = parse_url($location);
                        //download file
                        $connectionString = "ftp://anonymous:anonymous@10.1.1.33:21/pub/10.5524";
                        $conn_id = $this->getFtpConnection($connectionString);
                        echo "\n* connected to ftp server, ready to download file from $location...\n";
                        $download_status = ftp_get($conn_id,
                            "$local_dir/$preview_dir/$filename",
                            $location_parts['path'],
                            $this->get_ftp_mode($location_parts['path'])
                        );
                        ftp_close($conn_id);
                        //if too big, make small copy for files
                        //upload generated preview to s3.
                        //update redis
                    }
                    else {
                        throw new Exception("Failed touching the newly reserved job");
                    }

                }catch (Exception $loopex) {
                    echo "Error while processing job of id " . $job['id'] . ":" . $loopex->getMessage();
                    $consumer->bury($job['id'],0);
                    echo "The job of id: " . $job['id'] . " has been " . $consumer->statsJob($job['id'])['state'];
                }
            }



        }
        catch (Exception $runex) {
            echo "Error while initialising the worker: " . $ex->getMessage();
            $consumer->disconnect();
        }


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

    private static function random_string($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

}
