<?php

class MultiDownload extends CApplicationComponent {
    public $download_host; //the host from where file bundle is gonna be downloaded
    public $download_protocol; //protocol to access the download host, ie: 'ftp://'
    public $multidownload_job_queue; //name of the Beanstalkd tube to use
    public $temporary_directory;
    public $ftp_bundle_directory; //directory in the ftp document root where to make the bundle available
    public $feature_enabled; // To disable or enable the multi download feature
}
 ?>
