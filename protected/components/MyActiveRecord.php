<?php
/**
 * Model is the customized base CActiveRecord class.
 * All Model classes for this application should extend from this base class.
 */
class MyActiveRecord extends CActiveRecord
{

    private $newdataset="";
    /*public function afterSave(){
        $this->newdataset=Yii::app()->basePath."/scripts/data/dbchanges.txt";
        parent::afterSave();
        $fh = fopen($this->newdataset, 'w');
        fwrite($fh,  floor(microtime(true)));
        fclose($fh);
    }*/
}