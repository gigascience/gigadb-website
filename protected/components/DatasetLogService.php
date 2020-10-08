<?php

/**
 * Component service for entry logging in dataset_log table
 * Class DatasetLogService
 */
class DatasetLogService extends CApplicationComponent
{
    public function init()
    {
        parent::init();
    }

    /**
     * Create a log in the dataset_log table when dataset status is Published.
     * @param $id
     * @param $creator
     * @param $message
     */
    public function createDatasetLogEntry($id, $creator, $message)
    {
        $model = Dataset::model()->findByPk($id);
        if ($model->upload_status === "Published") {

            $datasetlog = new DatasetLog;
            $datasetlog->dataset_id = $id;
            $datasetlog->message = "Test save log to dataset_log";
            $datasetlog->creation_at = date("Y-m-d");
            $datasetlog->model = "Test model";
            $datasetlog->model_id = "Test id";
            $datasetlog->url = "Test url";
            if (!$curationlog->save())
                return false;
        }
    }
}

