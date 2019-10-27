<?php
/**
 * This action will make connection to File Upload Wizard REST API
 * in order to create Filedrop accounts for a dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AssignFTPBoxAction extends CAction
{
    public function run($id)
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
            "requester" => \User::model()->findByPk(344), //admin user
            "identifier"=> $id,
            "dataset" => new DatasetDAO(["identifier" => $id]),
            "dryRunMode"=>false,
            ]);

        $response = $filedropSrv->createAccount();
        $message = "";
        if ($response) {
        	$message = "A new drop box will be created for this dataset. It will take up to 5mn of minutes.";
        	Yii::app()->user->setFlash('success',$message);
        }
        else {
        	$message = "An error occured. Drop box not created";
        	Yii::app()->user->setFlash('error',$message);
        }

        $this->getController()->redirect('/adminDataset/admin/');
    }
}

?>