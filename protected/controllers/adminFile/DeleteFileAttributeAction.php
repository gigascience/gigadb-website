<?php

declare(strict_types=1);

/**
 * This action will delete file attributes in admin file update page
 */

class DeleteFileAttributeAction extends CAction
{
    public function run()
    {
        if (!Yii::app()->request->isPostRequest) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        $id = Yii::$app->request->post('id');
        if (!$id) {
            throw new CHttpException(400, 'Invalid request');
        }

        $attribute = FileAttributes::model()->findByPk($id);
        if (!$attribute) {
            throw new CHttpException(400, 'Invalid request');
        }

        $dataset_id = $attribute->file->dataset_id;
        $fileName = $attribute->file->name;
        $fileModel = get_class($attribute);
        $fileId = $attribute->file->id;
        $modelId = $attribute->id;
        $model = Dataset::model()->findByPk($dataset_id);
        if ($model->upload_status === "Published") {
            DatasetLog::createDatasetLogEntry((int)$dataset_id, $fileName, $fileModel, $modelId, $fileId);
        } else {
            CurationLog::createCurationLogEntry((int)$dataset_id, $fileName); //Pass in dataset_id returned from File object.
        }
        $attribute->delete();
        Yii::app()->end();
    }
}
