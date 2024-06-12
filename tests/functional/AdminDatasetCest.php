<?php

declare(strict_types=1);

class AdminDatasetCest
{
    /**
     * @param FunctionalTester     $I
     * @param \Codeception\Example $status
     *
     * @return void
     * @dataProvider statusDataProvider
     *
     */
    public function tryToStayOnUpdatePageWhenUpdateStatus(FunctionalTester $I, \Codeception\Example $status)
    {
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');
        $I->amOnPage('adminDataset/update/id/8');
        $I->selectOption('#Dataset_upload_status', $status[0]);
        $I->click('Save');
        $I->seeCurrentUrlEquals('/adminDataset/update/id/8');
    }

    protected function statusDataProvider()
    {
        return [
            ['AssigningFTPbox'],
            ['Private'],
            ['Published'],
        ];
    }
}
