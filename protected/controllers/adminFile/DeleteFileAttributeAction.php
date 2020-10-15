<?php
/**
 * This action will delete file attributes in admin file update page
 */

class DeleteFileAttributeAction extends CAction
{
    public function run()
    {
        if (!Yii::app()->request->isPostRequest)
            throw new CHttpException(404, "The requested page does not exist.");

        if (isset($_POST['id'])) {
            $attribute = FileAttributes::model()->findByPk($_POST['id']);

            if ($attribute) {
                $out = $attribute->file->dataset_id;
                $attribute->delete();
                CurationLog::createCurationLogEntry($out); //Pass in dataset_id returned from File object.
                Yii::app()->end();
            }
        }
    }
}
