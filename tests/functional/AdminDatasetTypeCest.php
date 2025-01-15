<?php

class AdminDatasetTypeCest
{
    public function tryToRemoveAllTypesAndFail(FunctionalTester $I)
    {
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');

        //Remove custom image for dataset of id 8
        $I->amOnPage('adminDataset/update/id/8');
        $I->uncheckOption("#Dataset_Genomic");
        $I->click('Save');

        $I->canSee('Fail to update your types');
    }

    public function tryToAddADatasetType(FunctionalTester $I)
    {
        $I->cantSeeInDatabase('dataset_type',
            ['dataset_id' => 8, 'type_id' => 1]
        );
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');

        //Remove custom image for dataset of id 8
        $I->amOnPage('adminDataset/update/id/8');
        $I->canSee('Epigenomic');
        $I->checkOption('#Dataset_Epigenomic');
        $I->click('Save');

        $I->seeInDatabase('dataset_type',
            ['dataset_id' => 8, 'type_id' => 1]
        );
    }

    public function tryToRemoveADatasetType(FunctionalTester $I)
    {
        $I->cantSeeInDatabase('dataset_type',
            ['dataset_id' => 8, 'type_id' => 1]
        );
        $I->seeInDatabase('dataset_type',
            ['dataset_id' => 8, 'type_id' => 2]
        );
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');

        //Remove custom image for dataset of id 8
        $I->amOnPage('adminDataset/update/id/8');
        $I->canSee('Epigenomic');
        $I->canSee('Genomic');
        $I->checkOption('#Dataset_Epigenomic');
        $I->uncheckOption('#Dataset_Genomic');
        $I->click('Save');

        $I->seeInDatabase('dataset_type',
            ['dataset_id' => 8, 'type_id' => 1]
        );

        $I->cantSeeInDatabase('dataset_type',
            ['dataset_id' => 8, 'type_id' => 2]
        );
    }
}
