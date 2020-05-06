<?php
/**
 * This action will trigger a post to File Upload Wizard's move file endpoint
 * that create a backend job for moving uploaded files to public ftp
 *
 * @param string $doi DOI of dataset under Curation for which the uploaded files needs moving to public ftp
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class MoveFilesAction extends CAction
{
    /**
     * {@inheritdoc}
     */
    public function run($doi)
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
            "identifier"=> $doi,
            "dataset" => new DatasetDAO(["identifier" => $doi]),
            "dryRunMode"=>false,
            ]);

        $filedrop = $filedropSrv->getAccounts($doi);
        Yii::log("About to trigger job to move files to public ftp for FiledropAccount {$filedrop['id']}","info");
        $filedrop = new DatasetFiledrop(
                        $filedrop["id"],
                        $filedropSrv,
                        Yii::$app->params['dataset_filedrop']
                    );

        $response = $filedrop->moveUploadedFiles();
        Yii::log(var_export($response,true),"debug");

        echo json_encode($response);
        Yii::app()->end();     
    }
}

?>