<?php

use yii\console\ExitCode;

/**
 * Class RCloneCest
 * Test Case for testing backup related operation using rclone command
 * @use BucketHelper for beforeSuite and afterSuite
 */
class RCloneCest {

    /**
     * @param FunctionalTester $I
     * @group rclone-setup
     */
    public function trySetup(\FunctionalTester $I) {
        $I->runShellCommand("rclone config dump");
        $rcloneConfigJ = $I->grabShellOutput();
        $configArray = json_decode($rcloneConfigJ,true);
        codecept_debug($configArray);
        $I->assertNotNull($configArray["gigadb-backup"]);
        $I->assertNotNull($configArray["gigadb-backup"]["access_key_id"]);
        $I->assertNotNull($configArray["gigadb-backup"]["secret_access_key"]);
        $I->assertEquals("cos.ap-guangzhou.myqcloud.com",$configArray["gigadb-backup"]["endpoint"]);
        $I->assertEquals("DEEP_ARCHIVE",$configArray["gigadb-backup"]["storage_class"]);
    }

    /**
     * @param FunctionalTester $I
     * @group rclone-setup
     */
    public function tryListTestBucket(\FunctionalTester $I) {
        $I->runShellCommand("rclone lsjson gigadb-backup:");
        $listJ = $I->grabShellOutput();
        $bucketList = json_decode($listJ, true);
        codecept_debug( $bucketList);
        $testBucketExists = false;
        foreach($bucketList as $bucket) {
            if (preg_match("/bucket1-\d+/", $bucket["Name"]))
                $testBucketExists = true;
        }
        $I->assertTrue($testBucketExists);

    }

}