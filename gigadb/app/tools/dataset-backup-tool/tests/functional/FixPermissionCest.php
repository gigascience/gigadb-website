<?php

use yii\console\ExitCode;

class FixPermissionCest
{
    /**
     * To make sure change the permission of the file to non globally readable before every test.
     */
    public function _before(\FunctionalTester $I) {
        $I->runShellCommand("chmod 000 /app/tests/_data/10.1234/100001_101009/100300/perm-not-ok.txt");
    }

    /**
     * To make sure change the permission of the file to globally readable after every test.
     */
    public function _after(\FunctionalTester $I) {
        $I->runShellCommand("chmod 644 /app/tests/_data/10.1234/100001_101009/100300/perm-not-ok.txt");
    }

    /**
     * list file with permission Ok
     * dummy file created with permission -rw-r--r--
     */
    public function listOkFilePermissions (\FunctionalTester $I) {
        $I->runShellCommand("ls -al /app/tests/_data/10.1234/100001_101009/100010/perm-ok.txt");
        $output = $I->grabShellOutput();
        $I->assertStringContainsString("-rw-r--r--", $output, "Ok file cannot be ls!");
    }

    /**
     * list file with permission not Ok
     * dummy file created with permission
     */
    public function listNotOkFilePermissions (\FunctionalTester $I) {
        $I->runShellCommand("ls -al /app/tests/_data/10.1234/100001_101009/100300/perm-not-ok.txt");
        $output = $I->grabShellOutput();
        $I->assertStringContainsString("----------", $output, "Not Ok file cannot be ls!");
    }

    /**
     * Change the file permission to Ok
     */
    public function changeNotOkFilePermissionToOk (\FunctionalTester $I) {
        $I->runShellCommand("find /app/tests/_data/10.1234/ ! -perm -g+r,u+r,o+r -exec chmod a+r {} \;");
        $I->runShellCommand("ls -al /app/tests/_data/10.1234/100001_101009/100300/perm-not-ok.txt");
        $output = $I->grabShellOutput();
        $I->assertStringContainsString("-r--r--r--", $output, "Not Ok file permission has not been changed!");
    }
}