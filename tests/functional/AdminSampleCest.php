<?php

declare(strict_types=1);

/**
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional AdminSampleCest
 */
class AdminSampleCest
{
    public function tryToUpdateSampleId(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');
        $I->amOnPage('/adminSample/update/id/151');
        $I->canSee('Update Sample 151');
        $I->seeInField('#Sample_name', 'A. vittata');
        $I->fillField('#Sample_name', 'test');
        $I->click('Save');

        $I->amOnPage('/adminSample/update/id/151');
        $I->seeInField('#Sample_name', 'test');
    }
}
