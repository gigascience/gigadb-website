<?php

declare(strict_types=1);

class AdminProjectCest
{
    /**
     * @param FunctionalTester $I
     *
     * @return void
     */
    public function tryToUpdateNameAndUrlOfAProject(FunctionalTester $I)
    {
        $previousUrl = $I->grabFromDatabase('project', 'url', ['id' => 2]);
        $previousName = $I->grabFromDatabase('project', 'name', ['id' => 2]);

        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');
        $I->click('Admin');
        $I->click('Projects');
        $I->canSee('Create New Project');
        $I->seeElement('.table tr:first-child td:last-child a.icon-update');
        $I->click('.table tr:first-child td:last-child a.icon-update');

        $I->fillField("#Project_url", 'http://www.genome10kmodified.org/');
        $I->fillField('#Project_name', 'modified name');
        $I->click('Save');

        $I->assertNotEquals($previousUrl, $I->grabFromDatabase('project', 'url', ['id' => 2]));
        $I->assertNotEquals($previousName, $I->grabFromDatabase('project', 'name', ['id' => 2]));
    }
}
