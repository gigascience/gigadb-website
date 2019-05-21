<?php

class MainHelper
{
    /**
     * @return null|User
     */
    public static function getLoggedUser()
    {
        return User::model()->findByPk(Yii::app()->user->id);
    }

    public static function getImagesDir()
    {
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'../../images';
    }

    public static function getFilesDir()
    {
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'../../files';
    }

    public static function getUploadsDir()
    {
        return Yii::getPathOfAlias('uploadPath');
    }

    public static function debug($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }

    public static function debugX($var)
    {
        self::debug($var);
        exit;
    }
}