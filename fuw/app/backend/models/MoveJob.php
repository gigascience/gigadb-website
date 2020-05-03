<?php

namespace backend\models;

use \Yii;

/**
 * yii2-queue job class (DTO) for moving files
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class MoveJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $doi;
    public $file;
    
    public function execute($queue)
    {
    	Yii::info("Enqueuing move job for {$this->file} ({$this->doi})");
    	$source = "@uploads/{$this->doi}/{$this->file}";
    	$dest = "@publicftp/{$this->doi}/{$this->file}";

    	if ( Yii::$app->fs->has($source) ) {		
	        $isSuccess = Yii::$app->fs->copy(
	        	Yii::getAlias($source), Yii::getAlias($dest)
	        );
    	}
    }
}

?>