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
        $I->resetEmails();
    }

    /**
     * Email containing activation link should be sent to new user after user
     * creation
     * 
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function trySendActivationEmail(FunctionalTester $I)
    {

        $targetUrl = "/user/create";

        // Fill in web form and submit
        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'User_email'], 'swordmaster@mailinator.com');
        $I->fillField(['id' => 'User_first_name'], 'Duuncan');
        $I->fillField(['id' => 'User_last_name'], 'Idaaho');
        $I->fillField(['id' => 'User_password'], 'foobar');
        $I->fillField(['id' => 'User_password_repeat'], 'foobar');
        $I->fillField(['id' => 'User_affiliation'], 'Atriedes');
        $I->selectOption('form select[id=User_preferred_link]', 'NCBI');
        $I->checkOption('#User_newsletter');
        $I->checkOption('#User_terms');
        $I->fillField(['id' => 'User_verifyCode'], 'ouch');
        $I->click('Register');
        // Pressing Register button results in GigaDB website
        // going to /user/welcome page
        $I->see('Welcome!', 'h2');
        // Extract URLs from activation email sent to new user
        $urls = $I->grabUrlsFromLastEmail();
        codecept_debug($urls);
        // These URLs should contain one user activation link
        $url_matches = preg_grep('/^http:\/\/gigadb.test\/user\/confirm\/key\/\d+?/', $urls);
        $I->assertCount(1, $url_matches, "User activation link in email was not found");
    }

    /**
     * Notification email should be sent to curators after activation by new 
     * user
     *
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function trySendNotificationEmail(FunctionalTester $I)
    {
        $targetUrl = "/user/create";

        // Fill in web form and submit
        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'User_email'], 'warmaster@mailinator.com');
        $I->fillField(['id' => 'User_first_name'], 'Gurney');
        $I->fillField(['id' => 'User_last_name'], 'Halleck');
        $I->fillField(['id' => 'User_password'], 'foobar');
        $I->fillField(['id' => 'User_password_repeat'], 'foobar');
        $I->fillField(['id' => 'User_affiliation'], 'Atriedes');
        $I->selectOption('form select[id=User_preferred_link]', 'NCBI');
        $I->checkOption('#User_newsletter');
        $I->checkOption('#User_terms');
        $I->fillField(['id' => 'User_verifyCode'], 'boom');
        $I->click('Register');
        // Check /user/welcome page
        $I->see('Welcome!', 'h2');
        // Extract user activation link
        $urls = $I->grabUrlsFromLastEmail();
        $url_matches = preg_grep('/^http:\/\/gigadb.test\/user\/confirm\/key\/\d+?/', $urls);
        // Go to activation link
        $I->amOnPage(array_values($url_matches)[0]);
        // Get Curator notification email
        $message = $I->getLastMessage();
        $content = $I->getMessageContent($message);
        // Check curator notification email contains expected message
        $I->assertStringContainsString("New user registration", $content, "Notification email does not contain expected message");
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
