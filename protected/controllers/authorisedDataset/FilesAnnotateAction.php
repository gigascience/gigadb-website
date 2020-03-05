<?php
/**
 * This action will load the metadata form
 *
 * URL: /authorisedDataset/filesAnnotate/100006
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */

use Yii;
use \yii\web\UploadedFile;

class FilesAnnotateAction extends CAction
{

    public function run($id)
    {
        $this->getController()->layout='uploader_layout';
        $webClient = new \GuzzleHttp\Client();

        // Instantiate FileUploadService and DatasetUpload
        $fileUploadSrv = new FileUploadService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => 3600,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => Yii::app()->user,
            "identifier"=> $id,
            "dataset" => new DatasetDAO(["identifier" => $id]),
            "dryRunMode"=>false,
            ]);

        $datasetUpload = new DatasetUpload(
            $fileUploadSrv->dataset, 
            $fileUploadSrv, 
            Yii::$app->params['dataset_upload']
        );
        // Fetch list of uploaded files
        $uploadedFiles = $fileUploadSrv->getUploads($id);

        // Fetch list of attributes
        $attributes = [];
        foreach($uploadedFiles as $upload) {
            $attributes[$upload['id']] = $fileUploadSrv->getAttributes($upload['id']);
        }

        $bulkStatus = false;
        if(isset($_FILES) && is_array($_FILES) && isset($_FILES["bulkmetadata"])) {
            Yii::log("File is attached",'warning');
            $postedFile = UploadedFile::getInstanceByName("bulkmetadata");
            Yii::log(var_export($postedFile,true));
            $postedFile->saveAs("/var/tmp/$id-".$postedFile->name);
            list($sheetData, $parseErrors) = $datasetUpload->parseFromSpreadsheet("text/csv","/var/tmp/$id-".$postedFile->name);
            if (isset($sheetData) && is_array($sheetData) && !empty($sheetData)) {
                list($newUploads, $attributes, $mergeErrors) = $datasetUpload->mergeMetadata($uploadedFiles, $sheetData);
                Yii::log(var_export($newUploads, true),'info');
                $bulkStatus = $fileUploadSrv->updateUploadMultiple($id,$newUploads);
                Yii::log("update Upload Multiple: ", $bulkStatus);
            }
            if($bulkStatus) {
                Yii::app()->user->setFlash('filesAnnotate','Metadata loaded');
            }
            foreach(array_merge($parseErrors, $mergeErrors) as $error) {
                 Yii::app()->user->addFlash('filesAnnotateErrors',$error);
            }

            $this->getController()->redirect(["authorisedDataset/annotateFiles", "id" => $id]);            
        }

        if(isset($_POST['DeleteList']))
        {
            $deletedlist = $fileUploadSrv->deleteUploads($_POST['DeleteList']);
            if(count($deletedlist)>0) {
                Yii::app()->user->setFlash('uploadDeleted',count($deletedlist).' File(s) successfully deleted');
            }

        }

        $allUploadsSaved = true;
        if(isset($_POST['Upload']))
        {
            foreach($uploadedFiles as $upload)
            {
                if(isset($_POST['Upload'][$upload['id']])) {
                    $allUploadsSaved = $allUploadsSaved && $fileUploadSrv->updateUpload($upload['id'], $_POST['Upload'][$upload['id']] );
                }
            }
        }

        $allAttributesSaved = true;
        if(isset($_POST['Attributes']))
        {
            foreach($uploadedFiles as $upload)
            {
                if(isset($_POST['Attributes'][$upload['id']])) {
                    $allAttributesSaved = $allAttributesSaved && $fileUploadSrv->setAttributes($upload['id'], $_POST['Attributes'][$upload['id']] );
                }
            }
        }

        if (Yii::$app->request->isPost && $allUploadsSaved && $allAttributesSaved) {

                $statusChangedAndNotified = $datasetUpload->setStatusToDataAvailableForReview(
                    $datasetUpload->renderNotificationEmailBody(
                        "DataAvailableForReview"
                    )
                );
                if($statusChangedAndNotified) {
                    Yii::app()->user->setFlash('fileUpload','File uploading complete');
                    $this->getController()->redirect("/user/view_profile#submitted");
                }
                else {
                    Yii::app()->user->setFlash('error','Error changing and notifying dataset upload status');
                }
                
        }
        elseif ( Yii::$app->request->isPost ) {
                Yii::app()->user->setFlash('error','Error with some files');
        }
        $this->getController()->render("filesAnnotate", array("identifier" => $id, "uploads" => $uploadedFiles, "attributes" => $attributes));
    }
}

?>