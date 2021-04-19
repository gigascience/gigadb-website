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
                $dataset_id = $attribute->file->dataset_id;
                $fileName = $attribute->file->name;
                $fileModel = get_class($attribute);
                $fileId = $attribute->file->id;
                $modelId = $attribute->id;
                $model = Dataset::model()->findByPk($dataset_id);
                if ($model->upload_status === "Published") {
                    DatasetLog::createDatasetLogEntry($dataset_id, $fileName, $fileModel, $modelId, $fileId);
                } else {
                    CurationLog::createCurationLogEntry($dataset_id, $fileName); //Pass in dataset_id returned from File object.
                }
                $attribute->delete();
                Yii::app()->end();
            }
        }
    }
}
