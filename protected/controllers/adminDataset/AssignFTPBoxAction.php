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
            "requester" => User::model()->findByPk(Yii::app()->user->id), //admin user
            "identifier"=> $id,
            "dataset" => new DatasetDAO(["identifier" => $id]),
            "dryRunMode"=>false,
            ]);

        $response = $filedropSrv->createAccount();
        if (!$response) {
        	Yii::app()->user->setFlash('error',"An error occured. Drop box not created");
            $this->getController()->redirect("/adminDataset/admin/");
        }

        Yii::app()->session["filedrop_id_".Yii::app()->user->id] = array($id, $response['id']);

        $this->getController()->redirect("/adminDataset/admin/");
    }
}

?>