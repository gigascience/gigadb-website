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
                $fileName = $attribute->file->name;
                $fileModel = get_class($attribute);
                $fileId = $attribute->file->id;
                $modelId = $attribute->id;
                $model = Dataset::model()->findByPk($out);
                if ($model->upload_status === "Published") {
                    DatasetLog::createDatasetLogEntry($out, $fileName, $fileModel, $modelId, $fileId);
                } else {
                    CurationLog::createCurationLogEntry($out); //Pass in dataset_id returned from File object.
                }
                $attribute->delete();
                Yii::app()->end();
            }
        }
    }
}
