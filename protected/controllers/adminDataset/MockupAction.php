<?php

declare(strict_types=1);

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
        if ("invalid" === $datasetPageSettings->getPageType()) {
            Yii::log("dataset is invalid","error");
            return $this->getController()->redirect('/site/index');
        } elseif ("public" === $datasetPageSettings->getPageType()) {
            Yii::log("Not making mockup for published dataset","error");
            Yii::app()->user->setFlash('error',"Not making mockup for published dataset");
           return $this->getController()->redirect('/adminDataset/update/id/'.$model->id);
        }

        $reviewerEmail = Yii::$app->request->post('revieweremail');
        $monthsOfValidity = Yii::$app->request->post('monthsofvalidity');
        // parse form parameter (expects revieweremail and monthsofvalidity)
        if (!$reviewerEmail) {
            Yii::log("revieweremail parameter is missing from _POST","error");
            Yii::app()->user->setFlash('error',"revieweremail parameter is missing from _POST");
            return $this->getController()->redirect('/adminDataset/update/id/'.$model->id);
        }
        elseif (!$monthsOfValidity) {
            Yii::log("monthsofvalidity parameter is missing from _POST","error");
            Yii::app()->user->setFlash('error',"monthsofvalidity parameter is missing from _POST");
            return $this->getController()->redirect('/adminDataset/update/id/'.$model->id);
        }

        $mockupTokenService = Yii::app()->fileUploadService->createTokenService();
        $filedropSrv = new FiledropService([
            "tokenSrv" => $mockupTokenService,
            "webClient" => \Yii::$container->get('guzzleHttpClient'),
            "requester" => Yii::app()->user,
            "identifier"=> $model->identifier,
            "dataset" => new DatasetDAO(["identifier" => $model->identifier]),
            "dryRunMode"=>false,
            ]);

        list($token, $user_id) = $filedropSrv->makeMockupUrl($mockupTokenService, $reviewerEmail, (int) $monthsOfValidity);

        // Add entry to curation log
        $curationlog = new CurationLog;
        $curationlog->creation_date = date("Y-m-d");
        $curationlog->created_by = "System";
        $curationlog->dataset_id = $id;
        $curationlog->action = "Mockup url created for $reviewerEmail for $monthsOfValidity months";
        $mockupUrl = Yii::app()->params['home_url'] . "/dataset/mockup/uuid/$token";
        $curationlog->comments = "Mockup url created for $reviewerEmail for $monthsOfValidity months at " . $mockupUrl;
        if (!$curationlog->save()) {
            Yii::log("Error saving Curation log entry for mockup creation on dataset_id $id","error");
            Yii::app()->user->setFlash('error',"Error saving Curation log entry for mockup creation");
        } else {
            Yii::app()->user->setFlash('success',"Unique ($reviewerEmail), time-limited ($monthsOfValidity months) mockup url ready at <a href=\"$mockupUrl\">$mockupUrl</a>");
        }

        return $this->getController()->redirect("/adminDataset/admin/");
    }
}

?>
