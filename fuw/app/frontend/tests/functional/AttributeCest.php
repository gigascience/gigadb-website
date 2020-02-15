<?php namespace frontend\tests\functional;
use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\UploadFixture;

use common\models\Upload;

use Yii;

/**
 * functional test for ReplaceAction.php
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AttributeCest
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
     * Testing POST on /attributes/
     *
     */
    public function setAttributes(FunctionalTester $I)
    {

        $example = [
            1 => [
                "Temperature" => ["value" => "45", "unit" => "Celsius"],
                "Humidity" => [ "value" => "75", "unit" => "%"],
                "Age" => ["value" => "33", "unit" => "Years"],
            ],
            2 => [
                "Contrast" => [ "value" => "3000", "unit" => "Nits"],
            ], 
        ];
        $I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
        $I->sendPOST("/attributes/replace_for/1", $example[1]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
  
    }
}
