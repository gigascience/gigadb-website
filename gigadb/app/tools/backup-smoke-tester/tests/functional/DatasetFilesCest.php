<?php

use Yii;
use yii\console\ExitCode;

class DatasetFilesCest {
    /**
     * Uploads 3 files in tests/_data/dataset1 into Tencent bucket
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
     * Uploads 1 changed file, test.csv from tests/_data/dataset2 into Tencent
     * bucket
     */
    public function tryUpdateBackupWithChangedFile(\FunctionalTester $I) {
        $dateStamp = "20210530";

        $upload_response = Yii::$app->createControllerByID('backup-smoke-test')->run('update-backup-with-changed-file',[
            "date" => $dateStamp,
        ]);
        codecept_debug($upload_response);

        // Get timestamp for test.csv
        $test_csv_info = Yii::$app->createControllerByID('backup-smoke-test')->run('view-file-info',[
            "date" => $dateStamp, "/dataset/test.csv",
        ]);
        $test_csv_pairs = $this->extractKeyValuePairs($test_csv_info);
        codecept_debug($test_csv_info);
        $test_csv_timestamp = strtotime($test_csv_pairs["Last-Modified"]);
        codecept_debug("test_csv_timestamp = ".$test_csv_timestamp);
        // Get timestamp for test.tsv
        $test_tsv_info = Yii::$app->createControllerByID('backup-smoke-test')->run('view-file-info',[
            "date" => $dateStamp, "/dataset/test.tsv",
        ]);
        $test_tsv_pairs = $this->extractKeyValuePairs($test_tsv_info);
        codecept_debug($test_tsv_info);
        $test_tsv_timestamp = strtotime($test_tsv_pairs["Last-Modified"]);
        codecept_debug("test_tsv_timestamp = ".$test_tsv_timestamp);
        // Compare timestamps
        $I->assertLessThan($test_csv_timestamp, $test_tsv_timestamp, "test.csv does not have an expected later timestamp");
    }

    /**
     * Uploads dataset3 directory into bucket
     *
     * The dataset3 directory does not contain test.tsv file which is therefore
     * deleted in the Tencent bucket.
     */
    public function tryUpdateBackupWithDeletedFile(\FunctionalTester $I) {
        $dateStamp = "20210530";

        $upload_response = Yii::$app->createControllerByID('backup-smoke-test')->run('update-backup-with-deleted-file', [
            "date" => $dateStamp,
        ]);
        codecept_debug($upload_response);

        $list_response = Yii::$app->createControllerByID('backup-smoke-test')->run('list-contents',[
            "date" => $dateStamp, "dataset/"
        ]);
        codecept_debug($list_response);
        $I->assertStringContainsString("dataset/readme_dataset.txt", $list_response, "readme_dataset.txt file is not in the COS directory");
        $I->assertStringContainsString("dataset/test.csv", $list_response, "test.csv file is not in the COS directory");
        $I->assertStringNotContainsString("dataset/test.tsv", $list_response, "test.tsv file is in COS directory");
    }

    /**
     * Convenience function to transform Tencent coscmd output into key-value
     * pairs for simpler querying
     */
    private function extractKeyValuePairs(String $tencent_output) {
        $out = preg_replace("/\s+\n\s+/", "&", trim($tencent_output));
        $out = preg_replace("/\s{2,}/", "=", trim($out));
        parse_str($out, $output);
        return $output;
    }
}