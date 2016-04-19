<?php 

class ActiveRecordLogableBehavior extends CActiveRecordBehavior
{
    public $_oldattributes = array();
 
    public function afterFind($event)
    {
        // Save old values
        $this->setOldAttributes($this->Owner->getAttributes());
    }
 
    public function getOldAttributes()
    {
        return $this->_oldattributes;
    }
 
    public function setOldAttributes($value)
    {
        $this->_oldattributes=$value;
    }

    public function createLog($dataset_id, $message)
    {
        #only save the log when dataset is public
        $dataset = Dataset::model()->findByPk($dataset_id);
        if($dataset->IsPublic) {
            $log = new DatasetLog;
            $log->dataset_id = $dataset_id;
            $log->message = $message;
            $log->model = $this->Owner->tableName();
            $log->model_id =  $this->Owner->id;
            $log->save(false);
        }
    }
}