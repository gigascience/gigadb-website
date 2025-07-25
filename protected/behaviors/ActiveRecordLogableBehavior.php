<?php

declare(strict_types=1);

/**
* @property CActiveRecord $Owner
*/
class ActiveRecordLogableBehavior extends CActiveRecordBehavior
{
    public array $_oldattributes = array();

    protected function afterFind($event):void
    {
        // Save old values
        $this->setOldAttributes($this->Owner->getAttributes());
    }

    public function getOldAttributes(): array
    {
        return $this->_oldattributes;
    }

    public function setOldAttributes(array $value): void
    {
        $this->_oldattributes = $value;
    }

    public function createLog(int $dataset_id, ?string $message = null): void
    {
        #only save the log when dataset is public
        $dataset = Dataset::model()->findByPk($dataset_id);
        if ($dataset->getIsPublic()) {
            $log = new DatasetLog();
            $log->dataset_id = $dataset_id;
            $log->message = $message;
            $log->model = $this->Owner->tableName();
            $log->model_id =  $this->Owner->id;
            $log->save(false);
        }
    }
}
