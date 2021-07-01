<?php

use Yii;
use yii\console\ExitCode;
use \app\models\DatasetFiles;

class DatasetFilesCest {

    public function setUp() {
        # load database schema in test database
        DatasetFiles::reloadDb("20210608", true);

        #make sure there's no call to the main runner in test files
        $outcome = system("grep -n './yii\s' /app/tests/functional/*");
        if($outcome) {
            exit("There are calls to the main runner in the test classes:\n $outcome\n");
        }
    }

    public function tearDown() {
    }

    /**
     * @group download-restore
     * @param FunctionalTester $I
     */
    public function tryDownloadRestoreBackupWithDateOption(\FunctionalTester $I) {
        $dateStamp = date('Ymd', strtotime(date('Ymd')." - 2 days"));

        $I->runShellCommand("echo yes | ./yii_test dataset-files/download-restore-backup --date $dateStamp");
        $I->canSeeInShellOutput("Downloading production backup for $dateStamp");
        $I->canSeeInShellOutput("Restoring the backup for $dateStamp");
        $I->seeResultCodeIs(Exitcode::OK);

        system("./yii_test dataset-files/download-restore-backup --config");

    }

    /**
     * @group download-restore
     * @param FunctionalTester $I
     */
    public function tryDownloadRestoreBackupWithDefaultNoDownloadOptions(\FunctionalTester $I) {
        $dateStamp = "20210608";

        $I->runShellCommand("echo yes | ./yii_test dataset-files/download-restore-backup --default --nodownload");
        $I->canSeeInShellOutput("Restoring the backup for $dateStamp");
        $I->seeResultCodeIs(Exitcode::OK);
    }

    /**
     * @group download-restore
     * @param FunctionalTester $I
     */
    public function tryDownloadRestoreBackupWithLatestOption(\FunctionalTester $I) {
        $dateStamp = date('Ymd', strtotime(date('Ymd')." - 1 day"));

        $I->runShellCommand("echo yes | ./yii_test dataset-files/download-restore-backup --latest");
        $I->canSeeInShellOutput("Downloading production backup for $dateStamp");
        $I->canSeeInShellOutput("Restoring the backup for $dateStamp");
        $I->seeResultCodeIs(Exitcode::OK);

    }

    /**
     * @group update-ftp-urls
     * @param FunctionalTester $I
     */
    public function tryUpdateFtpUrlNextAfter(\FunctionalTester $I) {
        $command = Yii::$app->createControllerByID('dataset-files');
        $outcome = $command->run('update-ftp-urls',[
            "next" => 5,
            "after" => 10,
            "dryrun" => true,
        ]);
        $I->assertEquals(Exitcode::OK, $outcome);
    }

    /**
     * @group update-ftp-urls
     * @param FunctionalTester $I
     */
    public function tryReplacementCommandUsageWhenNoOptions(\FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test dataset-files/update-ftp-urls", false);
        $I->canSeeInShellOutput("Usage:");
        $I->canSeeInShellOutput("dataset-files/update-ftp-url --next <batch size> [--after <dataset id>][--dryrun][--verbose]");
        $I->canSeeResultCodeIs(Exitcode::USAGE);
    }

    /**
     * @group download-restore
     * @param FunctionalTester $I
     */
    public function tryDownloadCommandUsageWhenNoOptions(\FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test dataset-files/download-restore-backup", false);
        $I->canSeeInShellOutput("Usage:");
        $I->canSeeInShellOutput("dataset-files/download-restore-backup --date 20210608 | --latest | --default [--nodownload]");
        $I->canSeeResultCodeIs(Exitcode::USAGE);
    }

    /**
     * @group update-ftp-urls
     * @param FunctionalTester $I
     */
    public function tryReplacementCommandWithPendingDatasetsProceed(\FunctionalTester $I)
    {
        $I->runShellCommand("echo yes | ./yii_test dataset-files/update-ftp-urls --next 5");
        $I->canSeeInShellOutput("Warning! This command will alter 5 datasets in the database, are you sure you want to proceed?");
        $I->canSeeInShellOutput("Executing command...");

    }

    /**
     * @group update-ftp-urls
     * @param FunctionalTester $I
     */
    public function tryReplacementCommandWithPendingDatasetsAbort(\FunctionalTester $I)
    {
        $I->runShellCommand("echo no | ./yii_test dataset-files/update-ftp-urls --next 5", false);
        $I->canSeeInShellOutput("Warning! This command will alter 5 datasets in the database, are you sure you want to proceed?");
        $I->canSeeInShellOutput("Aborting.");
        $I->canSeeResultCodeIs(ExitCode::NOPERM);

    }

    /**
     * @group update-ftp-urls
     * @param FunctionalTester $I
     */
    public function tryReplacementCommandNoPendingDatasets(\FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test dataset-files/update-ftp-urls --next 5 --after 99999");
        $I->canSeeInShellOutput("There are no pending datasets with url to replace.");
    }

    /**
     * @group update-ftp-urls
     * @param FunctionalTester $I
     */
    public function tryReplacementCommandShowsConfig(\FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test dataset-files/update-ftp-urls --config", false);
        $I->canSeeInShellOutput("[db] => Array");
        $I->canSeeInShellOutput("[ftp] => Array");
        $I->canSeeInShellOutput("pgsql:host=pg9_3;dbname=gigadb_test;port=5432");
        $I->canSeeResultCodeIs(ExitCode::CONFIG);
    }

    /**
     * @group download-restore
     * @param FunctionalTester $I
     */
    public function tryRestoreCommandShowsConfig(\FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test dataset-files/download-restore-backup --config", false);
        $I->canSeeInShellOutput("[db] => Array");
        $I->canSeeInShellOutput("[ftp] => Array");
        $I->canSeeInShellOutput("pgsql:host=pg9_3;dbname=gigadb_test;port=5432");

        $I->canSeeResultCodeIs(ExitCode::CONFIG);
    }

    /**
     * @group download-restore
     * @param FunctionalTester $I
     */
    public function tryRestoreCommandWithPendingDatasetsAbort(\FunctionalTester $I)
    {
        $I->runShellCommand("echo no | ./yii_test dataset-files/download-restore-backup --date 20210608", false);
        $I->canSeeInShellOutput("Warning! This command will drop the configured database (hosted on pg9_3) and restore it from the 20210608 backup, are you sure you want to proceed?");
        $I->canSeeInShellOutput("Aborting.");
        $I->canSeeResultCodeIs(ExitCode::NOPERM);
    }

}