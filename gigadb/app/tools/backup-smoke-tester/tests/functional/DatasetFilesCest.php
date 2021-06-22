<?php

use Yii;
use yii\console\ExitCode;

class DatasetFilesCest {
    /**
     * 
     */
    public function tryBackupDataset(\FunctionalTester $I) {
        $dateStamp = "20210530";

        $outcome = Yii::$app->createControllerByID('backup-smoke-test')->run('backup-dataset',[
            "date" => $dateStamp,
        ]);
        codecept_debug($outcome);
        
        $outcome = Yii::$app->createControllerByID('backup-smoke-test')->run('list-contents',[
            "date" => $dateStamp, "dataset/"
        ]);
        codecept_debug($outcome);
        $tokens = preg_split('/\s+/', trim($outcome));
        codecept_debug($tokens);
        $I->assertEquals("dataset/readme_dataset.txt", $tokens[0], "readme_dataset.txt file does not appear to have been uploaded");
        $I->assertEquals("dataset/test.csv", $tokens[5], "test.csv file does not appear to have been uploaded");
        $I->assertEquals("dataset/test.tsv", $tokens[10], "test.tsv file does not appear to have been uploaded");
    }

    /**
     *
     */
    public function tryUpdateBackupWithChangedFile(\FunctionalTester $I) {
        $dateStamp = "20210530";

        $outcome = Yii::$app->createControllerByID('backup-smoke-test')->run('update-backup-with-changed-file',[
            "date" => $dateStamp,
        ]);

        codecept_debug($outcome);

        // Get timestamp for test.csv
        $test_csv_info = Yii::$app->createControllerByID('backup-smoke-test')->run('view-file-info',[
            "date" => $dateStamp, "/dataset/test.csv",
        ]);
        $output1 = $this->extractKeyValuePairs($test_csv_info);
        codecept_debug($test_csv_info);
        $test_csv_timestamp = strtotime($output1["Last-Modified"]);
        print "test_csv_timestamp = ".$test_csv_timestamp;
        // Get timestamp for test.tsv
        $test_tsv_info = Yii::$app->createControllerByID('backup-smoke-test')->run('view-file-info',[
            "date" => $dateStamp, "/dataset/test.tsv",
        ]);
        $output2 = $this->extractKeyValuePairs($test_tsv_info);
        codecept_debug($test_tsv_info);
        $test_tsv_timestamp = strtotime($output2["Last-Modified"]);
        print "test_tsv_timestamp = ".$test_tsv_timestamp;
        // Compare timestamps
        $I->assertLessThan($test_csv_timestamp, $test_tsv_timestamp, "test.csv does not have an expected later timestamp");
    }

    /**
     */
    public function tryUpdateBackupWithDeletedFile(\FunctionalTester $I) {
        $dateStamp = "20210530";

        $outcome = Yii::$app->createControllerByID('backup-smoke-test')->run('update-backup-with-deleted-file', [
            "date" => $dateStamp,
        ]);
        codecept_debug($outcome);

        $outcome2 = Yii::$app->createControllerByID('backup-smoke-test')->run('list-contents',[
            "date" => $dateStamp, "dataset/"
        ]);
        codecept_debug($outcome2);
        $I->assertStringContainsString("dataset/readme_dataset.txt", $outcome2, "readme_dataset.txt file is not in the COS directory");
        $I->assertStringContainsString("dataset/test.csv", $outcome2, "test.csv file is not in the COS directory");
        $I->assertStringNotContainsString("dataset/test.tsv", $outcome2, "test.tsv file is in COS directory");
    }

    private function extractKeyValuePairs(String $tencent_output) {
        $out = preg_replace("/\s+\n\s+/", "&", trim($tencent_output));
        $out = preg_replace("/\s{2,}/", "=", trim($out));
        parse_str($out, $output);
        return $output;
    }
}