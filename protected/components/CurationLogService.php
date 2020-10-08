<?php

/**
 * Component service for entry logging in curation_log table
 * Class CurationLogServiceTest
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
    public function createNewEntry($id, $username, $message )
    {
        $model = new CurationLog;
        $model->creation_date=date("Y-m-d");
        $model->last_modified_date=null;
        $model->dataset_id=$id;
        $username = User::model()->find('id=:user_id', array(':user_id'=>Yii::app()->user->id));
        $username = $username->first_name.' '.$username->last_name;
        $model->created_by = $username;
        $model->action = $message;
        $model->save();
//            if($model->save())
//            {
//                $this->redirect(array('view','id'=>$model->id));
//            }
    }
}