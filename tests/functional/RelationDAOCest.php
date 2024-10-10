<?php

/**
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional RelationDAOCest
 */
class RelationDAOCest
{
    public function tryToCreateRelationWithoutReciprocalRelation(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );

        $I->canSee('Admin');
        $I->click('Admin');
        $I->click('Dataset:Relations');
        $I->click('Create A New Relation');
        $I->canSee('Create Relation');
        $I->selectOption('select#Relation_dataset_id', '5');
        $I->selectOption('select#Relation_related_doi', '100006');
        $I->selectOption('select#Relation_relationship_id', '13');
        $I->uncheckOption('#Relation_add_reciprocal');
        $I->click('Create');

        $I->seeInDatabase('relation',
            ['dataset_id' => 5, 'related_doi' => '100006', 'relationship_id' => 13]
        );
        $I->canSee('View Relation');
    }

    public function tryToCreateRelationWithReciprocalRelationAndFail(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );

        $I->canSee('Admin');
        $I->click('Admin');
        $I->click('Dataset:Relations');
        $I->click('Create A New Relation');
        $I->canSee('Create Relation');
        $I->selectOption('select#Relation_dataset_id', '5');
        $I->selectOption('select#Relation_related_doi', '100006');
        $I->selectOption('select#Relation_relationship_id', '13');
        $I->click('Create');

        $I->dontSeeInDatabase('relation',
            ['dataset_id' => 5, 'related_doi' => '100006', 'relationship_id' => 13]
        );

        $I->canSee('Failed as it was unable to save the reciprocal relation');
    }

    public function tryToCreateRelationWithReciprocalRelation(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );

        $I->canSee('Admin');
        $I->click('Admin');
        $I->click('Dataset:Relations');
        $I->click('Create A New Relation');
        $I->canSee('Create Relation');
        $I->selectOption('select#Relation_dataset_id', '5');
        $I->selectOption('select#Relation_related_doi', '100006');
        $I->selectOption('select#Relation_relationship_id', '1');
        $I->click('Create');

        $I->seeInDatabase('relation',
            ['dataset_id' => 5, 'related_doi' => '100006', 'relationship_id' => 1]
        );
        $I->seeInDatabase('relation',
            ['dataset_id' => 8, 'related_doi' => '100039', 'relationship_id' => 2]
        );
    }

    public function tryToCreateRelationWithSameDOIAndFail(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );

        $I->canSee('Admin');
        $I->click('Admin');
        $I->click('Dataset:Relations');
        $I->click('Create A New Relation');
        $I->canSee('Create Relation');
        $I->selectOption('select#Relation_dataset_id', '5');
        $I->selectOption('select#Relation_related_doi', '100039');
        $I->selectOption('select#Relation_relationship_id', '13');
        $I->click('Create');

        $I->dontSeeInDatabase('relation',
            ['dataset_id' => 5, 'related_doi' => '100006', 'relationship_id' => 13]
        );

        $I->canSee("Can't refer the same DOI");
    }
}
