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

    /**
     * @param FunctionalTester $I
     * @group rclone-setup
     */
    public function tryLoadVariables(\FunctionalTester $I) {
       $I->canSeeFileFound("/app/config/variables");
       $I->seeThisFileMatches("/BACKUP_LOCAL_ROOT=.+/");
       $I->seeThisFileMatches("/BACKUP_REMOTE_ROOT=.+/");
       $I->seeThisFileMatches("/BACKUP_BUCKET_FULLNAME=.+/");
       $I->cantSeeInThisFile("BACKUP_BUCKET_FULLNAME=cngbdb-share-backup-2-1255501786"); # don't use production bucket on dev/CI environment
    }

    /**
     * @param FunctionalTester $I
     * @group rclone-backup
     */
    public function tryBackupDataset(\FunctionalTester $I) {
        // Get variables from file
        $ini_array = parse_ini_file("/app/config/variables");
        $sourcedir = $ini_array['BACKUP_LOCAL_ROOT'];
        $destdir = $ini_array['BACKUP_REMOTE_ROOT'];
        $bucketname = $ini_array['BACKUP_BUCKET_FULLNAME'];

        $I->runShellCommand("rclone --verbose sync $sourcedir gigadb-backup:$bucketname$destdir 2>&1");
        codecept_debug($I->grabShellOutput());

        // List contents for bucket
        $I->runShellCommand("rclone lsjson gigadb-backup:$bucketname/$destdir");
        $listJ = $I->grabShellOutput();
        $I->assertStringContainsString("readme_dataset.txt", $listJ, "readme_dataset.txt file does not appear to have been uploaded");
        $I->assertStringContainsString("test.csv", $listJ, "test.csv file does not appear to have been uploaded");
        $I->assertStringContainsString("test.tsv", $listJ, "test.tsv file does not appear to have been uploaded");
    }

}