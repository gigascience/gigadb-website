<?php namespace frontend\tests\functional;
use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\UploadFixture;

use common\models\Upload;

use Yii;

class UploadCest
{
	/**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ],
            'upload' => [
                'class' => UploadFixture::className(),
                'dataFile' => codecept_data_dir() . 'upload_data.php'
            ]
        ];
    }

    /**
     * Testing GET on /uploads/ with additional params for doi
     *
     */
    public function getUpload(FunctionalTester $I)
    {

        // $doi = Yii::$app->security->generateRandomString(6);
        $doi ="010010";

        $I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
        $I->sendGET("/uploads",[  "filter[doi]" => $doi ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('doi' => "$doi"));
        $I->dontSeeResponseContainsJson(array('doi' => "010020"));
        $I->seeResponseJsonMatchesJsonPath('$.uploads[0].name');
        $I->seeResponseJsonMatchesJsonPath('$.uploads[1].name');
        $I->dontSeeResponseJsonMatchesJsonPath('$.uploads[2].name');
    }
}
