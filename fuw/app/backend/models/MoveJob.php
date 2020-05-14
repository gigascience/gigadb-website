<?php

namespace backend\models;

use \Yii;

/**
 * yii2-queue job class (DTO) for moving files
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class MoveJob extends \yii\base\Component implements \yii\queue\JobInterface
{
    public $doi;
    public $file;
    private $_fs;
    
    public function init()
    {
        $this->_fs = Yii::$app->fs ;
    }

    public function getFs()
    {
        return $this->_fs;
    }

    public function setFs($fs)
    {
        $this->_fs = $fs;
    }

    public function execute($queue)
    {
    	Yii::warning("Move job for {$this->file} ({$this->doi})");
    	$source = Yii::getAlias("@uploads/{$this->doi}/{$this->file}");
    	$dest = Yii::getAlias("@publicftp/{$this->doi}/{$this->file}");
    	
        Yii::warning("source: ".Yii::getAlias($source).", destination: ".Yii::getAlias($dest));
        $this->_fs->copy($source,$dest);
    }
}

?>