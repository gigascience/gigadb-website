<?php

/**
 * Class EmailCest
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional EmailCest.php
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional EmailCest
 */
class EmailCest
{
    public function _before(FunctionalTester $I, \Codeception\Scenario $scenario)
    {
        $config = require(__DIR__."/../../protected/config/yii2/web.php");
        if (!$config['components']['mailer']['transport']['username'])
            $scenario->skip('skipping on local dev');
        $I->resetEmails();
    }

    /**
     * Integration test to check email containing password reset link is sent
     * to new user after user creation
     *
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function trySendPasswordEmail(FunctionalTester $I)
    {
        // Create database record for user
        $id = $I->haveInDatabase('gigadb_user', [
            'email' => 'xyzzy@mailinator.com',
            'password' => '5a4f75053077a32e681f81daa8792f95',
            'first_name' => 'IAM',
            'last_name' => 'Xyzzy',
            'affiliation' => 'BGI',
            'role' => 'user',
            'is_activated' => 'true',
            'newsletter' => 'false',
            'previous_newsletter_state' => 'true',
            'username' => 'xyzzy@mailinator.com',
            'preferred_link' => 'EBI',
        ]);
        codecept_debug("gigadb user: ".$id);

        $targetUrl = "/site/forgot";

        // Fill in web form and submit
        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'ForgotPasswordForm_email'], 'xyzzy@mailinator.com');
        $I->click('Reset Password');
        // Pressing Register button results in GigaDB website
        // going to /site/thanks page
        $I->seeInCurrentUrl("/site/thanks");
        $I->see('Reset Password Request Submitted');
        // Now extract URLs from email sent to user
        $urls = $I->grabUrlsFromLastEmail();
        codecept_debug($urls);
        // These URLs should contain one user activation link
        $url_matches = preg_grep('/^http:\/\/gigadb.test\/site\/reset/', $urls);
        codecept_debug($url_matches);
        $I->assertCount(1, $url_matches, "User reset password link in email was not found");
    }

    /**
     * Integration test to check email containing activation link is sent to new
     * user after user creation
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
        $I->seeInCurrentUrl("/user/welcome");
        $I->see('Welcome!', 'h2');
        // Now extract URLs from activation email sent to new user
        $urls = $I->grabUrlsFromLastEmail();
        codecept_debug($urls);
        // These URLs should contain one user activation link
        $url_matches = preg_grep('/^http:\/\/gigadb.test\/user\/confirm\/key\/\d+?/', $urls);
        $I->assertCount(1, $url_matches, "User activation link in email was not found");
    }

    /**
     * Integration test to check notification email is sent to curators after
     * new user account activationtmail
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
        $I->seeInCurrentUrl("/user/welcome");
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
}
