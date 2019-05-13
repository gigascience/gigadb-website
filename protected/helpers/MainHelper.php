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
        return Yii::getPathOfAlias('webroot') ."/images/";
    }

    public static function getFilesDir()
    {
        return Yii::getPathOfAlias('webroot') ."/images/";
    }

    public static function getUploadsDir()
    {
        return self::getImagesDir() ."uploads/";
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