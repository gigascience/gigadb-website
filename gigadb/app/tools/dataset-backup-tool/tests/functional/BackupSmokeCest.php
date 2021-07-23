<?php

class BackupSmokeCest {
    /**
     * Uploads 3 files in tests/_data/dataset1 into Tencent bucket and check
     * files are displayed in a bucket directory listing
     */
    public function tryBackupDataset(\FunctionalTester $I) {
        $I->runShellCommand("coscmd --debug upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rs tests/_data/dataset1/ dataset/ 2>&1");
        codecept_debug($I->grabShellOutput());
        
        $output = $this->listBucketDirectory($I,"dataset/");
        $tokens = preg_split('/\s+/', trim($output));
        $I->assertEquals("dataset/readme_dataset.txt", $tokens[0], "readme_dataset.txt file does not appear to have been uploaded");
        $I->assertEquals("dataset/test.csv", $tokens[5], "test.csv file does not appear to have been uploaded");
        $I->assertEquals("dataset/test.tsv", $tokens[10], "test.tsv file does not appear to have been uploaded");
    }

    /**
     * Uploads 1 changed file, test.csv from tests/_data/dataset2 into Tencent
     * bucket
     */
    public function tryUpdateBackupWithChangedFile(\FunctionalTester $I) {
        $I->runShellCommand("coscmd --debug upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rs tests/_data/dataset2/ dataset/ 2>&1");
        codecept_debug($I->grabShellOutput());

        // Get timestamp for test.csv
        $output = $this->getBucketFileInfo($I,"/dataset/test.csv");
        $test_csv_pairs = $this->extractKeyValuePairs($output);
        $test_csv_timestamp = strtotime($test_csv_pairs["Last-Modified"]);
        codecept_debug("test_csv_timestamp = ".$test_csv_timestamp);

        // Get timestamp for test.tsv
        $tsv_output = $this->getBucketFileInfo($I,"/dataset/test.tsv");
        $test_tsv_pairs = $this->extractKeyValuePairs($tsv_output);
        $test_tsv_timestamp = strtotime($test_tsv_pairs["Last-Modified"]);
        codecept_debug("test_tsv_timestamp = ".$test_tsv_timestamp);
        // Compare timestamps
        $I->assertLessThan($test_csv_timestamp, $test_tsv_timestamp, "test.csv does not have an expected later timestamp");
    }

    /**
     * Uploads dataset3 directory into bucket and check test.tsv is not listed
     *
     * The dataset3 directory does not contain test.tsv file which is therefore
     * deleted in the Tencent bucket.
     */
    public function tryUpdateBackupWithDeletedFile(\FunctionalTester $I) {
        $I->runShellCommand("coscmd --debug upload -H '{\"x-cos-storage-class\":\"DEEP_ARCHIVE\"}' -rsy --delete tests/_data/dataset3/ dataset/ 2>&1");
        codecept_debug($I->grabShellOutput());

        $output = $this->listBucketDirectory($I,"dataset/");
        $I->assertStringContainsString("dataset/readme_dataset.txt", $output, "readme_dataset.txt file is not in the COS directory");
        $I->assertStringContainsString("dataset/test.csv", $output, "test.csv file is not in the COS directory");
        $I->assertStringNotContainsString("dataset/test.tsv", $output, "test.tsv file is in COS directory");
    }

    /**
     * @param String $directory
     * @return mixed
     */
    private function listBucketDirectory(\FunctionalTester $I, $directory) {
        $I->runShellCommand("coscmd list ".$directory." 2>&1");
        return $I->grabShellOutput();
    }

    /**
     * @param String $filepath
     * @return mixed
     */
    private function getBucketFileInfo(\FunctionalTester $I, $filepath) {
        $I->runShellCommand("coscmd info ".$filepath." 2>&1");
        return $I->grabShellOutput();
    }

    /**
     * Convenience function to transform Tencent coscmd output into key-value
     * pairs for simpler querying
     */
    private function extractKeyValuePairs(String $tencent_output) {
        $out = preg_replace("/\n/", "&", $tencent_output);
        $out = preg_replace("/\s{4,}/", "=", trim($out));
        parse_str($out, $pairs);
        return $pairs;
    }
}