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
class ResetPasswordCest
{
    public function _before(FunctionalTester $I)
    {
    }

    /**
     * Functional test to check user with expired token is able to create reset 
     * password request
     *
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryCreatePasswordResetRequestByUserWithExpiredToken(FunctionalTester $I)
    {
        // Create database record with expired token for test
        $I->haveInDatabase('reset_password_request', [
            'selector' => 'gO2qhU0gcxgdBTY3QAeu',
            'hashed_token' => 'AFbtmT/CPC4fbo9L7ze8DlSqvHHK3mQUFSwojSPGFy8=',
            'requested_at' => '1998-03-01 01:53:23.000000',
            'expires_at' => '1998-03-01 02:53:23.000000',
            'gigadb_user_id' => '23'
        ]);
    
        $targetUrl = "/site/forgot";

        // Fill in web form and submit
        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'ForgotPasswordForm_email'], 'test@mailinator.com');
        $I->click('Reset');
        // Pressing Reset button takes user to /site/thanks page
        $I->seeInCurrentUrl("/site/thanks");
        $I->see('Reset Password Request Submitted', 'h4');
        $I->see('For security reasons, we cannot tell you if the email you entered is valid or not.');
        // The selector below will be deleted because its token has
        // expired and will be replaced with a new selector token
        $I->dontSeeInDatabase('reset_password_request', ['selector' => 'gO2qhU0gcxgdBTY3QAeu', 'gigadb_user_id' => '23']);
    }

    /**
     * Functional test to check if too many reset password requests have been
     * made due to the presence of a valid reset token.
     *
     * @param FunctionalTester $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function tryTooManyPasswordResetRequests(FunctionalTester $I)
    {
        $targetUrl = "/site/forgot";

        // Fill in web form and submit
        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'ForgotPasswordForm_email'], 'admin@gigadb.org');
        $I->click('Reset');
        // Reset button takes user to /site/thanks page
        $I->seeInCurrentUrl("/site/thanks");
        // Same user requests another password reset 
        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'ForgotPasswordForm_email'], 'admin@gigadb.org');
        $I->click('Reset');
        // Reset button now goes to /site/forgot page with flash message
        $I->seeInCurrentUrl("/site/forgot");
        $I->see('Forgotten password', 'h4');
        $I->see('Too many password requests - please wait till current request expires');
    }

    /**
     * Test to check valid token is deleted after successful password reset
     * 
     * @param FunctionalTester $I
     * @return void
     */
    public function tryResetPasswordWithValidToken(FunctionalTester $I)
    {
        // Create database record with valid token for test
        $I->haveInDatabase('reset_password_request', [
            'selector' => 'MBakd7kAwQXim10Ka1Hw',
            'hashed_token' => '19P4d2SgN1t1ZqxgGKik5jFjZsUz0f/+HtlfiPIS5UM=',
            'requested_at' => '2022-03-01 01:53:23.000000',
            'expires_at' => '9999-03-01 02:53:23.000000',
            'gigadb_user_id' => '24'
        ]);

        // Check database contains the selector of the token we want to use
        $I->seeInDatabase('reset_password_request', ['selector' => 'MBakd7kAwQXim10Ka1Hw', 'gigadb_user_id' => '24']);

        // Fill in web form and submit
        $targetUrl = "/site/reset?token=MBakd7kAwQXim10Ka1Hwf5EEpZ4WpNdv9mkEjKWW";
        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'ResetPasswordForm_password'], 'bar');
        $I->fillField(['id' => 'ResetPasswordForm_confirmPassword'], 'bar');
        $I->click('Save');
        // Register button will send user to /site/login page
        $I->seeInCurrentUrl("/site/login");
        $I->see('Login', 'h4');
        // Check flash message
        $I->see('Your password has been successfully reset. Please login again.');
        // Check website has deleted selector so it cannot be used again
        $I->dontSeeInDatabase('reset_password_request', ['selector' => 'MBakd7kAwQXim10Ka1Hw', 'gigadb_user_id' => '24']);
    }
}
