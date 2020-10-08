<?php

/**
 * Component service for entry logging in curation_log table
 * Class CurationLogService
 */

class CurationLogService extends CApplicationComponent
{
    public function init()
    {
        parent::init();
    }

    /**
     * Create a log in the curation_log table.
     * @param $id
     * @param $creator
     * @param $message
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function createCurationLogEntry($id, $creator, $message )
    {
        $model = Dataset::model()->findByPk($id);
        // Save log to curation_log when dataset status is Published
        if ($model->upload_status !== "Published") {
            $curationlog = new CurationLog;
            $curationlog->creation_date = date("Y-m-d");
            $curationlog->last_modified_date = null;
            $curationlog->dataset_id = $id;
            $curationlog->created_by = "System";
            $curationlog->action = "Status changed to stuff";
            if (!$curationlog->save())
                return false;
        }
    }
}
?>
