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

}
