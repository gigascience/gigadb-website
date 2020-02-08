<?php namespace frontend\tests\functional;
use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\fixtures\UploadFixture;

use common\models\Upload;

use Yii;

class NotificationCest
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
        ];
    }

    /**
     * Testing POST on /notifications/emailSend with additional params for doi
     *
     */
    public function emailSend(FunctionalTester $I)
    {

        $sender = "me@gigadb.test";
        $recipient = "someone@example.test";
        $subject = "functional test for message notification";
        $content = Yii::$app->security->generateRandomString(6);

        $I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
        $I->sendPOST("/notifications/emailSend",[  
            "from" => $sender, 
            "to" => $recipient,
            "subject" => $subject,
            "content" => $content 
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(["status" => "sent","type" => "email", "environment" =>"development"]);

    }
}
