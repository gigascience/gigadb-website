<?php
/**
 * This action will load Uppy.io based file uploader for dataset
 *
 * URL: /authorisedDataset/uploadFiles/100006
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FilesUploadAction extends CAction
{

    public function run($id)
    {
        $this->getController()->layout='uploader_layout';

        // Instantiate FileUploadService and DatasetUpload
        $webClient = new \GuzzleHttp\Client();
        $fileUploadSrv = new FileUploadService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => 3600,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requesterEmail" => Yii::app()->user->email,
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

        $this->getController()->render("filesUpload", array(
        	"identifier" => $id, 
        	"tusd_path" => Yii::$app->params['dataset_filedrop']['tusd_path'],
        	"uploadsCount" => count($uploadedFiles)
        ));
    }
}

?>