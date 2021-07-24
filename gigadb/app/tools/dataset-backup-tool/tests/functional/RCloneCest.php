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
     * Uploads a set of files from a directory into bucket
     * 
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

        // Check bucket contents
        $I->runShellCommand("rclone lsjson gigadb-backup:$bucketname/$destdir");
        $listJ = $I->grabShellOutput();
        $I->assertStringContainsString("readme_dataset.txt", $listJ, "readme_dataset.txt file does not appear to have been uploaded");
        $I->assertStringContainsString("test.csv", $listJ, "test.csv file does not appear to have been uploaded");
        $I->assertStringContainsString("test.tsv", $listJ, "test.tsv file does not appear to have been uploaded");
    }

    /**
     * Uploads 1 changed file, test.csv from tests/_data/dataset2 into Tencent
     * bucket
     * 
     * @param FunctionalTester $I
     * @group rclone-backup
     */
    public function tryUpdateBackupWithChangedFile(\FunctionalTester $I) {
        // Get variables from file
        $ini_array = parse_ini_file("/app/config/variables");
        $dataset2dir = $ini_array['BACKUP_LOCAL_ROOT_DATASET2'];
        $destdir = $ini_array['BACKUP_REMOTE_ROOT'];
        $bucketname = $ini_array['BACKUP_BUCKET_FULLNAME'];
        
        // Use checksum to compare differences between corresponding files
        $I->runShellCommand("rclone --verbose sync --checksum --no-update-modtime --use-server-modtime $dataset2dir gigadb-backup:$bucketname$destdir 2>&1");
        $sync_output = $I->grabShellOutput();
        codecept_debug($sync_output);
        
        // Check only test.csv uploaded
        $I->assertStringContainsString("test.csv: Copied (replaced existing)", $sync_output, "test.csv does not appear to have been uploaded");
        $I->assertStringNotContainsString("test.tsv", $sync_output, "test.tsv should not have been uploaded");
        $I->assertStringNotContainsString("readme_dataset.txt", $sync_output, "readme_dataset.txt should not have been uploaded");
    }

    /**
     * Uploads dataset3 directory into bucket and check test.tsv is not listed
     *
     * The dataset3 directory does not contain test.tsv file which is therefore
     * deleted in the Tencent bucket.
     * 
     * @param FunctionalTester $I
     * @group rclone-backup
     */
    public function tryUpdateBackupWithDeletedFile(\FunctionalTester $I) {
        // Get variables from file
        $ini_array = parse_ini_file("/app/config/variables");
        $dataset3dir = $ini_array['BACKUP_LOCAL_ROOT_DATASET3'];
        $destdir = $ini_array['BACKUP_REMOTE_ROOT'];
        $bucketname = $ini_array['BACKUP_BUCKET_FULLNAME'];

        $I->runShellCommand("rclone --verbose sync --checksum $dataset3dir gigadb-backup:$bucketname$destdir 2>&1");
        $sync_output = $I->grabShellOutput();
        codecept_debug($sync_output);

        // Check only test.csv uploaded
        $I->assertStringContainsString("test.tsv: Deleted", $sync_output, "test.tsv has not been deleted");

        // Confirm bucket contents
        $I->runShellCommand("rclone --verbose ls gigadb-backup:$bucketname$destdir 2>&1");
        $ls_output = $I->grabShellOutput();
        codecept_debug($ls_output);
        $I->assertStringContainsString("readme_dataset.txt", $ls_output, "readme_dataset.txt should be present in bucket");
        $I->assertStringContainsString("test.csv", $ls_output, "test.csv should be present in bucket");
        $I->assertStringNotContainsString("test.tsv", $ls_output, "test.tsv should not be in bucket");
    }
}