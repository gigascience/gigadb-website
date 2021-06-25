<?php

use Yii;
use yii\console\ExitCode;
use \app\models\DatasetFiles;

class DatasetFilesCest {

    public function setUp() {
        # load database schema in test database
        DatasetFiles::reloadDb("20210608", true);
    }

    public function tearDown() {

    }

    public function tryDownloadRestoreBackup(\FunctionalTester $I) {
        $dateStamp = date('Ymd') - 1;

        $outcome = Yii::$app->createControllerByID('dataset-files')->run('download-restore-backup',[
            "date" => $dateStamp,
        ]);

        $I->assertEquals(Exitcode::OK, $outcome);

    }

    public function tryListPendingDatasetsNoOptions(\FunctionalTester $I) {
        $command = Yii::$app->createControllerByID('dataset-files');
        $outcome = $command->run('list-pending-datasets');
        $I->assertEquals(Exitcode::NOINPUT, $outcome);
    }

    public function tryListPendingDatasetsAll(\FunctionalTester $I) {
        $command = Yii::$app->createControllerByID('dataset-files');
        $outcome = $command->run('list-pending-datasets',[
            "all" => true,
        ]);
        $I->assertEquals(Exitcode::OK, $outcome);
    }

    public function tryListPendingDatasetsNext(\FunctionalTester $I) {
        $command = Yii::$app->createControllerByID('dataset-files');
        $outcome = $command->run('list-pending-datasets',[
            "next" => 5,
        ]);
        $I->assertEquals(Exitcode::OK, $outcome);
    }

    public function tryUpdateFtpUrlNextAfter(\FunctionalTester $I) {
        $command = Yii::$app->createControllerByID('dataset-files');
        $outcome = $command->run('update-ftp-urls',[
            "next" => 5,
            "after" => 10,
        ]);
        $I->assertEquals(Exitcode::OK, $outcome);
    }
}