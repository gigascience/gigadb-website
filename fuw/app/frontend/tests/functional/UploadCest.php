<?php namespace frontend\tests\functional;
use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\UploadFixture;

use common\models\Upload;
use yii\base\Model;

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
        $I->seeResponseJsonMatchesJsonPath('$.[0].name');
        $I->seeResponseJsonMatchesJsonPath('$.[1].name');
        $I->dontSeeResponseJsonMatchesJsonPath('$.[2].name');
    }

    /**
     * Testing PUT on /uploads/ with single upload data 
     *
     */
    public function updateSingleUpload(FunctionalTester $I)
    {
        $doi ="010010";
        $example = [ 
            1 => [ 'doi' => $doi, 'name' =>"somefile.txt",'datatype' => 'Text', 'description' => 'foo bar'],
            2 => [ 'doi' => $doi, 'name' =>"someimage.png",'datatype' => 'Image', 'description' => 'hello world'],
        ];
        $I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
        $I->sendPUT("/uploads/2", $example[2]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->canSeeResponseContainsJson(
            [ 'doi' => $doi, 'name' =>"someimage.png",'datatype' => 'Image', 'description' => 'hello world']
        );

    }

    /**
     * Testing PUT on /uploads/ with multiple upload data 
     *
     */
    public function updateMultipleUploads(FunctionalTester $I)
    {
        $doi ="010010";
        $example = [
            "Uploads" => [
                1 => [ 'doi' => $doi, 'name' =>"FieldDataMethods.docx",'datatype' => 'Script', 'description' => 'foo bar'],
                2 => [ 'doi' => $doi, 'name' =>"Measurements.csv",'datatype' => 'Protein sequence', 'description' => 'hello world'],
            ] 
        ];


        // $uploads = Upload::find()->where(["doi" => $doi])->indexBy('id')->all();
        // $isValid = Model::validateMultiple($uploads);
        // $getErrors = function ($element) {
        //     return $element->errors;
        // };
        // var_dump(array_map($getErrors,$uploads));
        // $I->assertTrue($isValid);


        $I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
        $I->sendPUT("/uploads/bulkedit_for_doi/010010", $example);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->canSeeResponseContainsJson(
            [ 'description' => 'foo bar']
        );        
        $I->canSeeResponseContainsJson(
            [ 'description' => 'hello world']
        );

    }
}
