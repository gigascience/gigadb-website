<?php

namespace app\tests\functional;

class WasabiUserCest
{
    /**
     * Teardown code that is run after each test
     *
     * Currently just removes the Wasabi user that was created by this test
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function _after(FunctionalTester $I)
    {
        // Delete bucket created in tryCreateBucket() function
        $I->runShellCommand("/app/yii_test wasabi-user/delete --username author-giga-d-23-00288");
        $I->seeResultCodeIs(0);
    }

    /**
     * Test actionCreate() function in WasabiUserController
     *
     * @param FunctionalTester $I
     */
    public function tryCreateUser(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test wasabi-user/create --username author-giga-d-23-00288");
        $I->seeResultCodeIs(0);
        $out = $I->grabShellOutput();
        $I->assertStringContainsString("user/author-giga-d-23-00288", $out);

        // Check author-giga-d-23-00288 user account has been created
        $I->runShellCommand('/app/yii_test wasabi-user/list-users');
        $listUsersOutput = $I->grabShellOutput();
        $I->assertStringContainsString('author-giga-d-23-00288', $listUsersOutput);
    }
}
