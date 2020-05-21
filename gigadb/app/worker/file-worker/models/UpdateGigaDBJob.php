<?php

namespace app\models;

/**
 * yii2-queue job class (DTO) for updating GigaDB tables following file synchronization
 * the queue messages to be listened to is "updateGigaDBqueue"
 * it retrieves metadata for an upload from the FUW API and create a file record
 * in GigaDB database.
 * The message will be for a file
 *
 * @param string $doi DOI to identify the dataset
 * @param array $file array of file data to add to GigaDB
 * @param array $file_attributes array of attributes data associated with the file
 * @param array $sample_ids array of samples associated with the file
 *  
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class UpdateGigaDBJob extends \yii\base\Component implements \yii\queue\JobInterface
{
    // Message data

    /**
     * @var string DOI identifier for the dataset */
    public $doi;
    /** 
     * @var array $file array of file data to add to GigaDB */
    public $file;
    /**
     * @var array $file_attributes array of attributes data associated with the file */
    public $file_attributes;
    /**
     * @var array $sample_ids array of samples associated with the file */
    public $sample_ids;
    

    public function execute($queue)
    {
        return false;
    }
}

?>