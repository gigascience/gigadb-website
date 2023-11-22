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
    public function tryToSaveChangesForPrivateDataset(FunctionalTester $I)
    {
        $I->amOnPage('/adminDataset/update/id/668');
        $I->canSee('Private');
        $I->canSee('Open Private URL');
        $I->seeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '32']);
        $I->seeElement('input', ['name' => 'Dataset[title]', 'type' => 'text', 'value' => 'supporting data for nothing in particular']);
        $I->fillField('Dataset[dataset_size]', '1024');
        $I->fillField('Dataset[title]', 'Test title');
        $I->click('Save');
        $I->seeInCurrentUrl('/adminDataset/update/id/668');
        $I->seeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '1024']);
        $I->cantSeeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '32']);
        $I->seeElement('input', ['name' => 'Dataset[title]', 'type' => 'text', 'value' => 'Test title']);
        $I->cantSeeElement('input', ['name' => 'Dataset[title]', 'type' => 'text', 'value' => 'supporting data for nothing in particular']);
        $I->canSeeInDatabase('dataset', [
            'dataset_size' => 1024,
            'title' => 'Test title'
        ]);
        $I->cantSeeInDatabase('dataset', [
            'dataset_size' => 32,
            'title' => 'supporting data for nothing in particular'
        ]);
    }
    public function tryToCancelChangesForPrivateDataset(FunctionalTester $I)
    {
        $I->amOnPage('/adminDataset/update/id/668');
        $I->canSee('Private');
        $I->canSee('Open Private URL');
        $I->seeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '32']);
        $I->seeElement('input', ['name' => 'Dataset[title]', 'type' => 'text', 'value' => 'supporting data for nothing in particular']);
        $I->fillField('Dataset[dataset_size]', '1024');
        $I->fillField('Dataset[title]', 'Test title');
        # Use the CSS locator for the "Cancel" button
        $I->click('a.btn[href="/adminDataset/admin"]');
        $I->seeInCurrentUrl('/adminDataset/admin');
        $I->see('Manage Datasets');
        $I->canSeeInDatabase('dataset', [
            'dataset_size' => 32,
            'title' => 'supporting data for nothing in particular'
        ]);
        $I->cantSeeInDatabase('dataset', [
            'dataset_size' => 1024,
            'title' => 'Test title'
        ]);
    }

    public function tryToSaveChangesForPublicDataset(FunctionalTester $I)
    {
        $I->amOnPage('/adminDataset/update/id/8');
        $I->canSee('Published');
        $I->cantSee('Open Private URL');
        $I->seeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '755815424']);
        $I->seeElement('input', ['name' => 'Dataset[title]', 'type' => 'text', 'value' => 'Genomic data from Adelie penguin (<em>Pygoscelis adeliae</em>). ']);
        $I->fillField('Dataset[dataset_size]', '1024');
        $I->fillField('Dataset[title]', 'Test title');
        $I->click('Save');
        $I->seeInCurrentUrl('/dataset/100006');
        $I->canSee('Test title');
        $I->canSeeInDatabase('dataset', [
            'dataset_size' => 1024,
            'title' => 'Test title'
        ]);
        $I->cantSeeInDatabase('dataset', [
            'dataset_size' => '755815424',
            'title' => 'Genomic data from Adelie penguin (<em>Pygoscelis adeliae</em>). '
        ]);
    }

    public function tryToCancelChangesForPublicDataset(FunctionalTester $I)
    {
        $I->amOnPage('/adminDataset/update/id/8');
        $I->canSee('Published');
        $I->cantSee('Open Private URL');
        $I->seeElement('input', ['name' => 'Dataset[dataset_size]', 'type' => 'text', 'value' => '755815424']);
        $I->seeElement('input', ['name' => 'Dataset[title]', 'type' => 'text', 'value' => 'Genomic data from Adelie penguin (<em>Pygoscelis adeliae</em>). ']);
        $I->fillField('Dataset[dataset_size]', '1024');
        $I->fillField('Dataset[title]', 'Test title');
        # Use the CSS locator for the "Cancel" button
        $I->click('a.btn[href="/adminDataset/admin"]');
        $I->seeInCurrentUrl('/adminDataset/admin');
        $I->see('Manage Datasets');
        $I->canSeeInDatabase('dataset', [
            'dataset_size' => 755815424,
            'title' => 'Genomic data from Adelie penguin (<em>Pygoscelis adeliae</em>). '
        ]);
        $I->cantSeeInDatabase('dataset', [
            'dataset_size' => '1024',
            'title' => 'Test title'
        ]);
    }
}
