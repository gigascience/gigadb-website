<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 20/02/2017
 * Time: 16:01
 */



class DirectoryListing extends GFtpFile
{
    public $isDirectory;
    public $location;


    public static function toDirectoryListing(GFtpFile $f, $location_path) {
        $dl = Yii::createComponent(array('class' => 'DirectoryListing',
                                                            'filename' => $f->filename,
                                                            'group' => $f->group,
                                                            'mdTime' => $f->mdTime,
                                                            'rights' => $f->rights,
                                                            'size' => $f->size,
                                                            'user' => $f->user
        ));

        $dl->location = $location_path.'/'.$dl->filename;
        $dl->isDirectory = substr($dl->rights, 0, 1) === 'd'?true:false;

        return $dl;
    }


}