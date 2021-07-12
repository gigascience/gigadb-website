<?php

use Yii;
use yii\console\ExitCode;

class DatasetFilesCest {

    public function tryDownloadRestoreBackup(\FunctionalTester $I) {
        $dateStamp = "20210530";

        $outcome = Yii::$app->createControllerByID('dataset-files')->run('download-restore-backup',[
            "date" => $dateStamp,
        ]);


        $I->assertEquals(Exitcode::OK, $outcome);

    }
}