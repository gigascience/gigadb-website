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
    public function run(string $id, int $fid)
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

        $recipient = $filedropSrv->dataset->getSubmitter()->email;
        $subject = "Instructions for using the filedrop account for dataset $id";
        $datasetFiledrop = new DatasetFiledrop(
                                $fid,
                                $filedropSrv,
                                Yii::$app->params['dataset_filedrop']
                            );
        $filedropAccount = $datasetFiledrop->getFiledropAccountDetails();
        $instructions = $datasetFiledrop->renderUploadInstructions($filedropAccount);
        $response = $datasetFiledrop->sendUploadInstructions($recipient, $subject, $instructions);
        if (!$response) {
        	$message = "Error: Filedrop Account ($fid) instructions not sent for dataset ($id)";
        	Yii::app()->user->setFlash('error',$message);
            $this->getController()->redirect("/adminDataset/admin/");
        }

        $message = "Instructions sent to $recipient.";
        // save it to the instructions log
        $log = new CurationLog();
        $log->setAttributes([
             "dataset_id" => Dataset::model()->findByAttributes(['identifier'=>$id])->id,
             "action" => "upload instructions sent",
             "comments" => $instructions,
             "created_by" => User::model()->findByPk(Yii::app()->user->id)->getFullName(),
             "creation_date" => new CDbExpression('NOW()')
            ]);
        if ($log->save()) {
            Yii::log("email instructions saved in curation log",'info');
        }
        else {
            Yii::log("problem saving email instructions in curation log:",'error');
            foreach ($log->getErrors() as $attr => $msg) {
                Yii::log("$attr: $msg",'error');
            }
        }
        Yii::app()->user->setFlash('success',$message);
        unset(Yii::app()->session["filedrop_id_".Yii::app()->user->id]);

        $this->getController()->redirect("/adminDataset/admin/");
    }
}

?>