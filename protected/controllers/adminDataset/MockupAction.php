<?php
/**
 * This action for AdminDatasetController will generate a mockup access
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class MockupAction extends CAction
{
    public function run($id)
    {
        Yii::log("adminDatasetController: Mockup action","info");
        $reviewerEmail = null;
        $monthsOfValidity = null;
        $model= Dataset::model()->findByPk($id);
        $datasetPageSettings = new DatasetPageSettings($model);
        if ( "invalid" === $datasetPageSettings->getPageType() ) {
            Yii::log("dataset is invalid","error");
            $this->getController()->redirect('/site/index');
        } elseif ( "public" === $datasetPageSettings->getPageType() ) {
            Yii::log("Not making mockup for published dataset","error");
           $this->getController()->redirect('/adminDataset/update/id/'.$model->id);
        }

        // parse form parameter (expects revieweremail and monthsofvalidity)
        if ( !isset($_POST['revieweremail']) || "" === $_POST['revieweremail']) {
            Yii::log("revieweremail parameter is missing from _POST","error");
            Yii::app()->user->setFlash('error',"revieweremail parameter is missing from _POST");
            $this->getController()->redirect('/adminDataset/update/id/'.$model->id);
        }
        elseif ( !isset($_POST['monthsofvalidity']) ) {
            Yii::log("monthsofvalidity parameter is missing from _POST","error");
            Yii::app()->user->setFlash('error',"monthsofvalidity parameter is missing from _POST");
            $this->getController()->redirect('/adminDataset/update/id/'.$model->id);
        }

        $reviewerEmail = $_POST['revieweremail'];
        $monthsOfValidity = $_POST['monthsofvalidity'];
        

        $mockupTokenService = new TokenService([
                          'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                          'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                          'dt' => new DateTime(),
                        ]);

        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => 3600,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => new \GuzzleHttp\Client(),
            "requester" => Yii::app()->user,
            "identifier"=> $model->identifier,
            "dataset" => new DatasetDAO(["identifier" => $model->identifier]),
            "dryRunMode"=>false,
            ]);

        $token = $filedropSrv->makeMockupUrl($mockupTokenService, $reviewerEmail, $monthsOfValidity);

        // Add entry to curation log
        $curationlog = new CurationLog;
        $curationlog->creation_date = date("Y-m-d");
        $curationlog->created_by = "System";
        $curationlog->dataset_id = $id;
        $curationlog->action = "Mockup url created for $reviewerEmail for $monthsOfValidity months";
        $curationlog->comments = "Mockup url created for $reviewerEmail for $monthsOfValidity months at http://gigadb.test/dataset/mockup/$token";
        if (!$curationlog->save()) {
            Yii::log("Error saving Curation log entry for mockup creation on dataset_id $id","error");
        }

        // Show a flash message
        Yii::app()->user->setFlash('success',"Unique ($reviewerEmail), time-limited ($monthsOfValidity months) mockup url ready at http://gigadb.test/dataset/mockup/$token");

        $this->getController()->redirect("/adminDataset/admin/");
    }
}

?>