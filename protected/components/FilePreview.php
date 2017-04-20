<?php

class FilePreview extends CApplicationComponent {
    public $supported_media_types ; // array list of mime type for which a preview version can be created and made available
    public $preview_job_queue ; // name of the Beanstalkd job queue for preview creation job
    public $temporary_directory ; //file system location where to download file to preview and where to write generatd preview files
    public $preview_bucket ; // AWS S3 bucket to use for preview files
}
 ?>
