<?php

namespace backend\models;

use \Yii;
use common\models\Upload;
use \app\models\UpdateGigaDBJob;

/**
 * yii2-queue job class (DTO) for moving files
 *
 * @uses \app\models\UpdateGigaDBJob;
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class MoveJob extends \yii\base\Component implements \yii\queue\JobInterface
{
    /** @var $doi identifier this file is associated with */
    public $doi;
    /** @var $file name of the file to move */
    public $file;
    /** @var  $filedrop id of filedrop to ensure we don't pick up a file of the same name from a terminated filedrop for same doi */
    public $filedrop;

    /** 
    * @var $_fs Create a local proxy of Yii::$app->fs so we can inject mock in unit test */
    private $_fs;
    /** 
    * @var $_gigaDBQueue Create a local proxy of Yii::$app->updateGigaDB so we can inject mock in unit test */
    private $_gigaDBQueue;
    
    public function init()
    {
        $this->_fs = Yii::$app->fs ;
        $this->_gigaDBQueue = Yii::$app->updateGigaDBqueue ;
    }

    public function getFs()
    {
        return $this->_fs;
    }

    public function setFs($fs)
    {
        $this->_fs = $fs;
    }


    public function getGigaDBQueue()
    {
        return $this->_gigaDBQueue;
    }

    public function setGigaDBQueue($gigaDBQueue)
    {
        $this->_gigaDBQueue = $gigaDBQueue;
    }

    /**
     * Executed by the queue listener: copy files, change status and push GigaDB job
     * @param yii\queue\Queue $queue
     */
    public function execute($queue)
    {
    	Yii::warning("Move job for {$this->file} (DOI:{$this->doi}, filedrop id: {$this->filedrop})");
    	$source = Yii::getAlias("@uploads/{$this->doi}/{$this->file}");
    	$dest = Yii::getAlias("@publicftp/{$this->doi}/{$this->file}");
    	$timestamp = (new \DateTime())->format('U');

        $filesPublicUrl = Yii::$app->params['dataset_filedrop']["files_public_url"] ?? "http://localhost";

        if ( $this->_fs->has($dest) ) {
            $this->_fs->rename($dest, $dest.".todelete.$timestamp");
        }
        Yii::warning("source: $source, destination: $dest");
        if ( $this->_fs->copy($source,$dest) ) {
            $upload = Upload::findOne(["filedrop_account_id" => $this->filedrop, "name" => $this->file]);
            if (!isset($upload)) {
                Yii::error("Cannot find an Upload record for DOI {$this->doi} and file name {$this->file}");
                return false;
            }
            $upload->status = Upload::STATUS_SYNCHRONIZED;
            $upload->location = "$filesPublicUrl/{$this->doi}/{$this->file}";

            $isSaved = $upload->save();
            if(!$isSaved) {
                Yii::error($upload->errors);
                foreach ($upload->errors as $error) {
                    throw new \Exception(implode("\n",$error));
                }
                throw new \Exception($error);
            }
            return $upload->save() && $this->_gigaDBQueue->push($this->createUpdateGigaDBJob($upload));

        }
        return false;
    }

    /** 
     * Create a job class to be pushed into the updateGigaDBqueue
     * @param \common\models\Upload $upload Upload instance to serialize
     * @return \app\models\UpdateGigaDBJob
     */
    public function createUpdateGigaDBJob(\common\models\Upload $upload): ?UpdateGigaDBJob
    {
        if($upload) {
            $updateJob = new UpdateGigaDBJob();
            $updateJob->doi = $this->doi;
            $updateJob->file = $upload->attributes;
            $updateJob->file_attributes = $upload->uploadAttributes;
            $updateJob->sample_ids = array_map('trim',explode(",",$upload->sample_ids));
            Yii::warning("Created instance of UpdateGigaDBJob for file".$upload->name." for dataset ".$this->doi);
            return $updateJob;
        }
        Yii::error("Upload record is null");
        return $upload;
    }
}

?>