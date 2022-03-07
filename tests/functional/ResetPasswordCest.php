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
        $targetUrl = "/site/forgot";

        // Fill in web form and submit
        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'ForgotPasswordForm_email'], 'expired_token@mailinator.com');
        $I->click('Reset');
        // Pressing Register button results in GigaDB website going to /site/thanks page
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
        $I->fillField(['id' => 'ForgotPasswordForm_email'], 'too_many_requests@mailinator.com');
        $I->click('Reset');
        // Pressing Register button results in GigaDB website
        // going to /site/forgot page
        $I->seeInCurrentUrl("/site/forgot");
        $I->see('Forgotten password', 'h4');
        $I->see('Too many password requests - please wait till current request expires');
    }

    /**
     * Token is deleted after successful password reset
     * 
     * @param FunctionalTester $I
     * @return void
     */
    public function tryResetPasswordWithValidToken(FunctionalTester $I)
    {
        $targetUrl = "/site/reset?token=MBakd7kAwQXim10Ka1Hwf5EEpZ4WpNdv9mkEjKWW";

        // Fill in web form and submit
        $I->amOnPage($targetUrl);
        $I->fillField(['id' => 'ResetPasswordForm_password'], 'gigadb');
        $I->fillField(['id' => 'ResetPasswordForm_confirmPassword'], 'gigadb');
        $I->click('Save');
        // Pressing Register button results in GigaDB website
        // going to /site/login page
        $I->seeInCurrentUrl("/site/login");
        $I->see('Login', 'h4');
        $I->see('Your password has been successfully reset. Please login again.');
        // The selector below will be deleted so it cannot be used again
        // because its token has been successfully used
        $I->dontSeeInDatabase('reset_password_request', ['selector' => 'MBakd7kAwQXim10Ka1Hw', 'gigadb_user_id' => '24']);

    }
}
