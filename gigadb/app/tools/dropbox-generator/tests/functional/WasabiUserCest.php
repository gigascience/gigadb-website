<?php

namespace app\tests\functional;

use Codeception\Attribute\Depends;
use FunctionalTester;
use Yii;

class WasabiUserCest
{
    /*
     * @var string Part of credentials used to access Wasabi API
     */
    public string $accessKeyId;

    /*
     * @var string Wasabi username for author to use
     */
    public string $authorUserName = 'author-giga-d-23-00288';

    /**
     * Teardown code that is run after entire suite of tests has executed
     *
     * Currently just removes the Wasabi user that was created by this test
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function _after(FunctionalTester $I)
    {
        // Delete user access key created in tryCreateUser() function
        $I->runShellCommand("/app/yii_test wasabi-user/delete-access-key --access-key-id {$this->accessKeyId} --username {$this->authorUserName}");
        $I->seeResultCodeIs(0);

        // Delete user created in tryCreateUser() function
        $I->runShellCommand("/app/yii_test wasabi-user/delete --username {$this->authorUserName}");
        $I->seeResultCodeIs(0);
    }

    /**
     * Test actionCreate() function in WasabiUserController
     *
     * @param FunctionalTester $I
     */
    public function tryCreateUser(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test wasabi-user/create --username {$this->authorUserName}");
        $I->seeResultCodeIs(0);
        $out = $I->grabShellOutput();
        $I->assertStringContainsString("user/{$this->authorUserName}", $out);

        // Check author-giga-d-23-00288 user account has been created
        $I->runShellCommand('/app/yii_test wasabi-user/list-users');
        $listUsersOutput = $I->grabShellOutput();
        $I->assertStringContainsString($this->authorUserName, $listUsersOutput);

        $I->runShellCommand("/app/yii_test wasabi-user/create-access-key --username {$this->authorUserName}");
        $I->seeResultCodeIs(0);
        $this->accessKeyID = $I->grabShellOutput();
        codecept_debug("AccessKey: {$this->accessKeyId}");
    }
}
