<?php
/**
 * This action will make connection to File Upload Wizard REST API
 * in order to send email instructions for the new filedrop account
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class SendInstructionsAction extends CAction
{
    public function run($id, $fid)
    {
    	$jwt_ttl = 3600 ;
    	$webClient = new \GuzzleHttp\Client();


        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => $jwt_ttl,
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
                                $fid,
                                $filedropSrv,
                                Yii::$app->params['dataset_upload']
                            );
        $instructions = $datasetUpload->getDefaultUploadInstructions();
        $subject = "Instructions for using the filedrop account for dataset $id";

        $response = $filedropSrv->emailInstructions($fid, $subject, $instructions);
        $message = "";
        if (!$response) {
        	$message = "Error: Filedrop Account ($fid) instructions not sent for dataset ($id)";
        	Yii::app()->user->setFlash('error',$message);
            $this->getController()->redirect("/adminDataset/admin/");
        }

        $message = "Instructions sent.";
        Yii::app()->user->setFlash('success',$message);
        unset(Yii::app()->session["filedrop_id_".Yii::app()->user->id]);

        $this->getController()->redirect("/adminDataset/admin/");
    }
}

?>