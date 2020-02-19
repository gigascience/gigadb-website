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