<?php

/**
 * Component service for entry logging in curation_log table
 * Class CurationLogService
 */

class CurationLogService extends CApplicationComponent
{
    /**
     * Create a log in the curation_log table.
     * @param $id
     * @param $creator
     * @param $message
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function createNewEntry($id, $creator, $message )
    {
        $model = new CurationLog;
        $model->creation_date=date("Y-m-d");
        $model->last_modified_date=null;
        $model->dataset_id=$id;
        $model->created_by = $creator;
        $model->action = $message;
        $model->save();
//            if($model->save())
//            {
//                $this->redirect(array('view','id'=>$model->id));
//            }
    }
}