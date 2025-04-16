<?php

declare(strict_types=1);

class AdminExternalLinkCest
{
    public function tryToCreateAnArchiveLinkWithOrigin(FunctionalTester $I)
    {
        //https://archive.softwareheritage.org/browse/directory/d9323c56a707dc8e9fbea583c86fbec8d40b50c5/?origin_url=https://github.com/kircherlab/ReMM
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');

        $I->amOnPage('/adminExternalLink/create');
        $I->fillField(['name' => 'ExternalLink[url]'], 'https://github.com/kircherlab/ReMM');
        $I->selectOption('form select[id=ExternalLink_dataset_id]', '8');
        $I->selectOption('form select[id=ExternalLink_external_link_type_id]', '7');
        $I->click('Create');

        $I->canSee('View ExternalLink');
        $I->canSee('https://github.com/kircherlab/ReMM');
        $I->canSee('7');
        $I->canSee('8');

        $I->amOnPage('/adminExternalLink/create');
        $I->fillField(['name' => 'ExternalLink[url]'], 'https://archive.softwareheritage.org/browse/directory/d9323c56a707dc8e9fbea583c86fbec8d40b50c5/?origin_url=https://github.com/kircherlab/ReMM');
        $I->selectOption('form select[id=ExternalLink_dataset_id]', '8');
        $I->selectOption('form select[id=ExternalLink_external_link_type_id]', '11');
        $I->click('Create');

        $I->canSee('View ExternalLink');
        $I->canSee('Related Link');
        $I->canSee('https://github.com/kircherlab/ReMM');

        $id = $I->grabFromDatabase('external_link', 'id', [ 'url' => 'https://github.com/kircherlab/ReMM', 'is_referred' => true, 'dataset_id' => 8]);
        $I->seeInDatabase('external_link', ['url' => 'https://archive.softwareheritage.org/browse/directory/d9323c56a707dc8e9fbea583c86fbec8d40b50c5/?origin_url=https://github.com/kircherlab/ReMM', 'related_id' => $id]);
    }

    public function tryToRemoveAnArchiveLinkedWithAnOriginUrl(FunctionalTester $I)
    {
        $I->seeInDatabase('external_link', ['url' => 'https://github.com/cihga39871/Atria', 'dataset_id' => 2342, 'external_link_type_id' => 7, 'is_referred' => 1]);
        $I->seeInDatabase('external_link', ['url' => 'https://archive.softwareheritage.org/browse/directory/d9323c56a707dc8e9fbea583c86fbec8d40b50c5/?origin_url&#x3D;https://github.com/cihga39871/Atria', 'dataset_id' => 2342, 'external_link_type_id' => 11, 'related_id' => 1526]);

        $id = $I->grabFromDatabase('external_link', 'id', [ 'url' => 'https://archive.softwareheritage.org/browse/directory/d9323c56a707dc8e9fbea583c86fbec8d40b50c5/?origin_url&#x3D;https://github.com/cihga39871/Atria', 'dataset_id' => 2342, 'external_link_type_id' => 11, 'related_id' => 1526]);
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');

        $I->amOnPage('/adminExternalLink/update/id/'.$id);
        $I->selectOption('form select[id=ExternalLink_dataset_id]', '8');
        $I->click('Save');

        $I->cantSeeInDatabase('external_link', ['url' => 'https://github.com/cihga39871/Atria', 'dataset_id' => 2342, 'external_link_type_id' => 7, 'is_referred' => 1]);
        $I->cantSeeInDatabase('external_link', ['url' => 'https://archive.softwareheritage.org/browse/directory/d9323c56a707dc8e9fbea583c86fbec8d40b50c5/?origin_url&#x3D;https://github.com/cihga39871/Atria', 'dataset_id' => 2342, 'external_link_type_id' => 11, 'related_id' => 1526]);

        $I->seeInDatabase('external_link', ['url' => 'https://github.com/cihga39871/Atria', 'dataset_id' => 2342, 'external_link_type_id' => 7, 'is_referred' => false]);
        $I->seeInDatabase('external_link', ['url' => 'https://archive.softwareheritage.org/browse/directory/d9323c56a707dc8e9fbea583c86fbec8d40b50c5/?origin_url&#x3D;https://github.com/cihga39871/Atria', 'dataset_id' => 8, 'external_link_type_id' => 11]);
    }
}
