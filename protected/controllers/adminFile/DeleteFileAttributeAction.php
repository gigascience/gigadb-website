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
                $file = new File;
                $file->id = $attribute->file_id;
                $out = print_r($file->dataset_id, true);
                file_put_contents('fileobj.log', $out);
                $attribute->delete();
                CurationLog::createCurationLogEntry(8);
                Yii::app()->end();
            }
        }
    }
}
