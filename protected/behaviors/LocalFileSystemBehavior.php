<?php
Class LocalFileSystemBehavior extends CBehavior
{
    function rrmdir($dir) {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
           }
         }
         reset($objects);
         rmdir($dir);
       }
    }

    function ungzip($fromFile, $toFile) {
        $zp = @gzopen($fromFile, "r");
        $fp = @fopen($toFile, "w");
        while(!@gzeof($zp)) {$string = @gzread($zp, 4096); @fwrite($fp, $string, strlen($string));}
        @gzclose($zp);
        @fclose($fp);
    }

    function extension_to_mime_type($ext) {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'py' => 'text/x-python',
            'pl' => 'text/x-script.perl',
            'java' => 'text/x-java-source',
            'css' => 'text/css',
            'js' => 'text/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'gz' => 'application/x-gzip',
            'tgz' => 'application/x-tar-gz',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',

            //tabular
            'tsv' => 'text/plain',
            'csv' => 'text/plain',

            //bio
            'fa' => 'text/plain',
            'fq' => 'text/plain',
            'faa' => 'text/plain',
            'fasta' => 'text/plain',
            'fastq' => 'text/plain',
            'sam' => 'text/plain',
            'bam' => 'application/x-samtools',
            'cram' => 'application/x-samtools',
            'hdf5' => 'application/x-hdf5',
            'raw' => 'text/plain',
            'mzml' => 'application/xml',
            'mzxml' => 'application/xml',

        );

        if ( array_key_exists($ext, $mime_types) ) {
            return $mime_types[$ext];
        }

        return false;

    }

}
