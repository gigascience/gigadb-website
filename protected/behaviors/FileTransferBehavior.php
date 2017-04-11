<?php
Class FileTransferBehavior extends CBehavior
{


    function init() {

        $this->attachBehavior("loggable", new LoggableCommandBehavior() );
        parent::init();
    }

    function buildConnectionString() {

        $config = Yii::app()->getComponents(false);
        if (isset($config['ftp']) && isset($config['ftp']['connectionString'])) {
            $ftp_server_credentials = $config['ftp']['connectionString'] ;
            $initial_remote_dir = isset($config['mds']['doi']) ? "/pub/" . $config['mds']['doi'] : "/pub/10.5524" ;
            $connectionString = $ftp_server_credentials . $initial_remote_dir;
            return $connectionString;
        }
        else {
            return false ;
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
            case 'frn':case 'raw':case 'fa':case 'mzml':case 'mzxml':case 'csv':
                return FTP_ASCII;
        }
        return FTP_BINARY;
    }

    function ftp_getdir ($conn_id, $dir, $dataset_id) {

        $errors = [];

        if ($dir != ".") {
            if (ftp_chdir($conn_id, $dir) == false) {
                $this->log(("Change Dir Failed: $dir"),  pathinfo(__FILE__, PATHINFO_FILENAME) );
                return false;
            }
            $portable_path = str_replace("/pub/10.5524/100001_101000/$dataset_id/","", $dir);
            // $this->log("current dir: ". getcwd() . "\n",  pathinfo(__FILE__, PATHINFO_FILENAME) );
            // $this->log("ftp_getdir: mkdir($portable_path, true)\n",  pathinfo(__FILE__, PATHINFO_FILENAME) );
            if (!(is_dir($portable_path)))
                mkdir($portable_path, 0777, true);
            chdir ($portable_path);
        }

        $contents = ftp_nlist($conn_id, ".");
        foreach ($contents as $file) {

            if ($file == '.' || $file == '..')
                continue;

            if (@ftp_chdir($conn_id, $file)) {
                ftp_chdir ($conn_id, "..");
                $getdir_status = $this->ftp_getdir ($conn_id, $file, $dataset_id);
                if (false === $getdir_status) {
                    $errors[] =  $file ;
                }
            }
            else {
                $get_response = ftp_get($conn_id, $file, $file, $this->get_ftp_mode($file));
                if (false === $get_response) {
                    $errors[] =  $file ;
                }
            }
        }

        ftp_chdir ($conn_id, "..");
        chdir ("..");

        if(empty($errors)) {
            return true;
        }
        else {
            return false;
        }

    }
}
