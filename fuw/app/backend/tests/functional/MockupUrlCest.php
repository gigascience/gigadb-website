<?php 

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;

use common\models\MockupUrl;
use backend\models\DockerManager;

use Yii;
use Ramsey\Uuid\Uuid;

class MockupUrlCest
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
            ]
        ];
    }

    /**
     * functional test http post to add new mockup url
     *
     * @param FunctionalTester $I
     */
    public function addMockupUrl(FunctionalTester $I)
    {

        // what's in the token:
        // {
        //   "sub":"API Access request from client",
        //   "iss": "www.gigadb.org",
        //   "aud": "fuw.gigadb.org",
        //   "email": "sfriesen@jenkins.info",
        //   "name": "John Smith",
        //   "admin_status": "true",
        //   "role": "create",
        //   "iat" : "1561730823",
        //   "nbf" : "1561730823",
        //   "exp" : "2729513220"
        // }
        $uuid = Uuid::uuid4();
        $token = Yii::$app->security->generateRandomString(300);
        $I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
        $I->sendPOST("/mockup-urls",['url_fragment' =>$uuid->toString(), "jwt_token" => $token]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson(array('url_fragment' => $uuid->toString()));

    }
}
