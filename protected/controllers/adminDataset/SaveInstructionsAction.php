<?php
/**
 * This action will make connection to File Upload Wizard REST API
 * in order to save custom instructions for the new filedrop account
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class SaveInstructionsAction extends CAction
{
    public function run(string $id, int $fid)
    {
    	$jwt_ttl = 3600 ;
    	$webClient = new \GuzzleHttp\Client();

        $instructions = $_POST['instructions'];

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

        $datasetFiledrop = new DatasetFiledrop(
                                $fid,
                                $filedropSrv,
                                Yii::$app->params['dataset_filedrop']
                            );
        $response = $datasetFiledrop->changeUploadInstructions($instructions);
        if (!$response) {
        	$message = "Error: Filedrop Account ($fid) instructions not saved for dataset ($id)";
        	Yii::app()->user->setFlash('error',$message);
            $this->getController()->redirect("/adminDataset/admin/");
        }

        $message = "New instructions saved.";
        Yii::app()->user->setFlash('success',$message);
        unset(Yii::app()->session["filedrop_id_".Yii::app()->user->id]);

        $this->getController()->redirect("/adminDataset/admin/");
    }
}

?>