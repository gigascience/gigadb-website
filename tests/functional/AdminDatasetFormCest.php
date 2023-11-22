<?php

/**
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional AdminDatasetFormCest
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional AdminDatasetFormCest
 *
 */
class AdminDatasetFormCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
            'LoginForm[username]' => 'admin@gigadb.org',
            'LoginForm[password]' => 'gigadb']);
        $I->canSee('Admin');
    }

    // tests
    public function tryToSaveChangesForPrivatePage(FunctionalTester $I)
    {
        $I->amOnPage('/adminDataset/update/id/668');
        $I->canSee('Private');
        $I->canSee('Open Private URL');
        $I->seeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '32']);
        $I->canSee('well now, how to describe nothing in particular?');
        $I->fillField('Dataset[dataset_size]', '1024');
        $I->fillField('Dataset[description]', 'Test description');
        $I->click('Save');
        $I->seeInCurrentUrl('/adminDataset/update/id/668');
        $I->seeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '1024']);
        $I->cantSeeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '32']);
        $I->canSee('Test description');
        $I->cantSee('well now, how to describe nothing in particular');
        $I->canSeeInDatabase('dataset', [
            'dataset_size' => 1024,
            'description' => 'Test description'
        ]);
        $I->cantSeeInDatabase('dataset', [
            'dataset_size' => 32,
            'description' => 'well now, how to describe nothing in particular?'
        ]);
    }
    public function tryToCancelChangesForPrivatePage(FunctionalTester $I)
    {
        $I->amOnPage('/adminDataset/update/id/668');
        $I->canSee('Private');
        $I->canSee('Open Private URL');
        $I->seeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '32']);
        $I->canSee('well now, how to describe nothing in particular?');
        $I->fillField('Dataset[dataset_size]', '1024');
        $I->fillField('Dataset[description]', 'Test description');
        # Use the CSS locator for the "Cancel" button
        $I->click('a.btn[href="/adminDataset/admin"]');
        $I->seeInCurrentUrl('/adminDataset/admin');
        $I->see('Manage Datasets');
        $I->canSeeInDatabase('dataset', [
            'dataset_size' => 32,
            'description' => 'well now, how to describe nothing in particular?'
        ]);
        $I->cantSeeInDatabase('dataset', [
            'dataset_size' => 1024,
            'description' => 'Test description'
        ]);
    }
}
