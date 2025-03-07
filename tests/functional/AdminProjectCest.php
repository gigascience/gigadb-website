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

    /**
     * @param FunctionalTester $I
     *
     * @return void
     */
    public function tryToUpdateOnlyImageLocation(FunctionalTester $I)
    {
        $previousImageLocation = $I->grabFromDatabase('project', 'image_location', ['id' => 2]);
        $previousUrl = $I->grabFromDatabase('project', 'url', ['id' => 2]);
        $previousName = $I->grabFromDatabase('project', 'name', ['id' => 2]);

        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );

        $I->amOnPage('/adminProject/update/id/2');
        $I->fillField('#Project_image_location', 'http://www.modified.org/');
        $I->click('Save');

        $I->assertEquals($previousUrl, $I->grabFromDatabase('project', 'url', ['id' => 2]));
        $I->assertEquals($previousName, $I->grabFromDatabase('project', 'name', ['id' => 2]));
        $I->assertNotEquals($previousImageLocation, $I->grabFromDatabase('project', 'image_location', ['id' => 2]));
    }

    /**
     * @param FunctionalTester $I
     *
     * @return void
     */
    public function testCantUpdateProjectWithDuplicatedUrlAndName(FunctionalTester $I)
    {
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );

        $I->seeInDatabase('project', [
            'id'   => 2,
            'url'  => 'http://www.genome10k.org/',
            'name' => 'Genome 10K'
        ]);

        $I->seeInDatabase('project', [
            'id'   => 3,
            'url'  => 'http://avian.genomics.cn/en/index.html ',
            'name' => 'The Avian Phylogenomic Project'
        ]);

        $I->amOnPage('/adminProject/update/id/2');

        $I->fillField('#Project_url', 'http://avian.genomics.cn/en/index.html ');
        $I->fillField('#Project_name', 'The Avian Phylogenomic Project');

        $I->click('Save');

        $I->canSee('This name already exists.');
        $I->canSee('This url already exists.');
    }
}
