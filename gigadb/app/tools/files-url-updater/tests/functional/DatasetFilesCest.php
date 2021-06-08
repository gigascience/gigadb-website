<?php

use Yii;
use yii\console\ExitCode;

class DatasetFilesCest {

    public function setUp() {

        # load database schema in test database
        $dbConfig = \Yii::$app->params['db'];
        system("docker-compose run --rm updater psql -h {$dbConfig['host']} -U {$dbConfig['username']} {$dbConfig['test_database']} -f /app/sql/gigadb_tables.sql", $restoreStatus);

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
}