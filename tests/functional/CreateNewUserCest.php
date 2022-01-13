<?php

/**
 * Class CreateNewUserCest
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional CreateNewUserCest.php
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional CreateNewUserCest
 */
class CreateNewUserCest
{
    public function _before(FunctionalTester $I)
    {
//        $I->resetEmails();
    }

    /**
     * Activation email should be sent to new user after user creation
     * 
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function trySendActivationEmailUponCreateUser(FunctionalTester $I)
    {
        // print_r("eml dir: ".\Yii::$app->mailer->fileTransportPath);
        
        $targetUrl = "/user/create";

        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'User_email'], 'new@mailinator.com');
        $I->fillField(['id' => 'User_first_name'], 'Duuncan');
        $I->fillField(['id' => 'User_last_name'], 'Idaaho');
        $I->fillField(['id' => 'User_password'], '123456787');
        $I->fillField(['id' => 'User_password_repeat'], '123456787');
        $I->fillField(['id' => 'User_affiliation'], 'GigaScience');
        $I->selectOption('form select[id=User_preferred_link]', 'NCBI');
        $I->checkOption('#User_newsletter');
        $I->checkOption('#User_terms');
        $I->fillField(['id' => 'User_verifyCode'], 'shazam');
        $I->click('Register');
        $I->see('Welcome!', 'h2');
//        $message = $I->getLastMessage();
//        $content = $I->getMessageContent($message);
        $arr = $I->grabUrlsFromLastEmail();
        print_r($arr);
        $m_array = preg_grep('/^http:\/\/gigadb.test\/user\/confirm\/key\/\d+?/', $arr);
        print_r($m_array);
        $I->assertCount(1, $m_array);
    }

    /**
     * Notification email should be sent to curators after user creation
     *
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function trySendNotificationEmailUponCreateUser(FunctionalTester $I)
    {
        // print_r("eml dir: ".\Yii::$app->mailer->fileTransportPath);

        $targetUrl = "/user/create";

        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'User_email'], 'new@mailinator.com');
        $I->fillField(['id' => 'User_first_name'], 'Duuncan');
        $I->fillField(['id' => 'User_last_name'], 'Idaaho');
        $I->fillField(['id' => 'User_password'], '123456787');
        $I->fillField(['id' => 'User_password_repeat'], '123456787');
        $I->fillField(['id' => 'User_affiliation'], 'GigaScience');
        $I->selectOption('form select[id=User_preferred_link]', 'NCBI');
        $I->checkOption('#User_newsletter');
        $I->checkOption('#User_terms');
        $I->fillField(['id' => 'User_verifyCode'], 'shazam');
        $I->click('Register');
        $I->see('Welcome!', 'h2');
//        $message = $I->getLastMessage();
//        $content = $I->getMessageContent($message);
        $arr = $I->grabUrlsFromLastEmail();
        print_r($arr);
        $m_array = preg_grep('/^http:\/\/gigadb.test\/user\/confirm\/key\/\d+?/', $arr);
//        print_r($m_array);
//        $I->assertCount(1, $m_array);
        echo($m_array[0]);
    }

    /**
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryLoadDistinctCaptchaOnContactForm(FunctionalTester $I)
    {
        $targetUrl = "/site/contact";

        # load target url
        $I->amOnPage($targetUrl);
        # find captcha image
        $I->seeElement("//div/img[@style='width:200px;']");
        # Get the source url of the image
        $imgSrc1 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
        # make sure it's a PNG image
        $img_size = getimagesize("http://gigadb.test".$imgSrc1);
        $I->assertEquals("image/png", $img_size['mime']);

        # download content of captcha url
        $I->amOnPage($imgSrc1);
        $img1 = $I->checksumOfResponse();
        # load the target url again
        $I->amOnPage($targetUrl);
        # download content of captcha url
        $imgSrc2 = $I->grabAttributeFrom("//div/img[@style='width:200px;']",'src');
        $I->amOnPage($imgSrc2);
        $img2 = $I->checksumOfResponse();
        # make sure both content are different
        $I->assertNotEquals($img1, $img2);
    }
}
