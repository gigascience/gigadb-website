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
        // $I->resetEmails();
    }

    /**
     * Functional test to check when too many reset password requests have been
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
}
