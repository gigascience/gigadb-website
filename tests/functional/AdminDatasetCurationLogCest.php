<?php

declare(strict_types=1);

/**
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional AdminDatasetCurationLogCest
 */
class AdminDatasetCurationLogCest
{
    public function checkCurationLogIsNotUpdatedIfCuratorNotUpdated(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->amOnPage('adminDataset/update/id/5');
        $I->seeNumberOfElements('.table tbody tr', 1);
        $I->canSee('No results found.');
        $I->checkOption('#Dataset_Epigenomic');
        $I->click('Save');

        $I->canSee('Updated successfully!');
        $I->cantSee('No results found.');
        $I->canSee('Status changed to ImportFromEM');
        $I->seeNumberOfElements('.table tbody tr', 1);
    }

    public function checkCurationLogIsUpdatedIfCuratorIsUpdated(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->amOnPage('adminDataset/update/id/5');
        $I->seeNumberOfElements('.table tbody tr', 1);
        $I->canSee('No results found.');
        $I->selectOption('form select[id=Dataset_curator_id]', 988);
        $I->click('Save');

        $I->canSee('Updated successfully!');

        $I->cantSee('No results found.');
        $I->canSee('Status changed to ImportFromEM');
        $I->canSee('Curator Assigned: Chris A');
        $I->seeNumberOfElements('.table tbody tr', 2);
    }
}
