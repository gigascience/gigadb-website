<?php


namespace app\models;

use \Yii;

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
    
    /** 
    * @var $_self stand-in for $this to allow partial mocking in tests */
    private $_self;

    public function init()
    {
        $this->self = $this;
    }

    public function getSelf()
    {
        return $this->_self;
    }

    public function setSelf($self)
    {
        $this->_self = $self;
    }

    public function execute($queue)
    {
        Yii::warning("Update GigaDB for {$this->file['name']} ({$this->doi})");
        $someSaved = true;
        $dataset = Dataset::findOne(['identifier' => $this->doi]);
        if ($dataset) {
            if("Curation" !== $dataset->upload_status) {
                throw new \Exception("Dataset with DOI {$this->doi} has wrong status, Curation needed, got: {$dataset->upload_status}");
            }
            $fileId = $this->self->saveFile($dataset->id);
            $someSaved = $this->self->saveAttributes($fileId);
            return $fileId && $someSaved && $this->self->saveSamples($fileId);
        }
        throw new \Exception("Dataset record not found for DOI {$this->doi}");

    }

    /**
     * save into file table the metadata associated with a FUW upload
     *
     * @param int $datasetId dataset to which to attach the new file
     * @return int id of newly saved file
     **/
    public function saveFile(int $datasetId): int
    {
        Yii::warning("Creating file record for file".$this->file['name']." for dataset ".$this->doi);
        $file = new File();
        $file->dataset_id = $datasetId;
        $file->attributes = $this->file;
        $file->format_id = FileFormat::findOne(['name' => $this->file['extension'] ])->id;
        $file->type_id = FileType::findOne(['name' => $this->file['datatype'] ])->id;
        if(!$file->type_id) {
            $error = "Type ID could not be determined for ".$this->file['datatype'];
            Yii::error($error);
            throw new \Exception($error);
        }
        if(!$file->save()) {
            Yii::error($file->errors);
            foreach ($file->errors as $error) {
                throw new \Exception(implode("\n",$error));
            }
        }
        return $file->id;
    }

    /**
     * save file attribute associated to a file that was part of upload metadata
     *
     * @param int $fileId id of file record this file attributes are to be associated with
     * @return bool whether at least one file attribute has been saved 
     **/
    public function saveAttributes(int $fileId): bool
    {
        Yii::warning("Creating file_attributes record for {$this->file['name']} ({$this->doi})");
        $someSaved = false;
        foreach($this->file_attributes as $attr) {
            $fileAttr = new FileAttributes();
            $fileAttr->file_id = $fileId;
            $fileAttr->attribute_id = Attribute::findOne(["attribute_name" => $attr["name"]])->id;
            $fileAttr->unit_id = Unit::findOne(["name" => $attr["unit"]])->id;
            $fileAttr->value = $attr["value"];
            if(!$fileAttr->save()) {
                Yii::error($fileAttr->errors);
            }
            else {
                $someSaved = true;
            }
        }
        return $someSaved;
    }

    /**
     * save file samples associated to a file that was part of upload metadata
     *
     * @param int $fileId id of file record this file attributes are to be associated with
     * @return bool whether at least one file sample has been saved 
     **/
    public function saveSamples(int $fileId): bool
    {
        $someSaved = false;
        foreach($this->sample_ids as $sample_id) {
            Yii::warning("Creating file_sample associated to {$this->file['name']} ({$this->doi}) for sample $sample_id");
            $sample = Sample::findOne(["name" => $sample_id]);
            if ($sample) {
                $fs = new FileSample();
                $fs->file_id = $fileId;
                $fs->sample_id = $sample->id;
                if(!$fs->save()) {
                    Yii::error($fs->errors);
                }
                else {
                    $someSaved = true;
                }
            }
            else {
                Yii::error("no sample found for $sample_id");
            }
        }
        return $someSaved;
    }
}

?>