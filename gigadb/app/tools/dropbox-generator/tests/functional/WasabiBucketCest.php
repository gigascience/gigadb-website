<?php

namespace app\tests\functional;

use FunctionalTester;

class WasabiBucketCest
{
    /*
     * @var string Bucket name to use in tests
     */
    public string $bucket = 'bucket-giga-d-23-00288';

    /*
     * @var string Bucket name to use in tests
     */
    public string $bucketNameWithUpperCaseLetters = 'bucket-GIGA-D-23-00288';

    /**
     * Teardown code that is run after each test
     *
     * Currently just removes the Wasabi user that was created by this test
     *
     * @return void
     */
    public function _after(FunctionalTester $I)
    {
        codecept_debug("In _after() function");
        $I->runShellCommand("/app/yii_test wasabi-bucket/list-buckets");
        $bucketString = $I->grabShellOutput();
        $bucketNames = explode(PHP_EOL, $bucketString);
        codecept_debug($bucketNames);
        if (in_array("$this->bucket", $bucketNames)) {
            $I->runShellCommand("/app/yii_test wasabi-bucket/delete --bucketName {$this->bucket}");
            $I->seeResultCodeIs(0);
        }
    }

    /**
     * Test actionCreate function in WasabiBucketController
     *
     * @param FunctionalTester $I
     */
    public function tryCreateBucket(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test wasabi-bucket/create --bucketName {$this->bucket}");
        $I->seeResultCodeIs(0);
        $out = $I->grabShellOutput();
        $I->assertStringContainsString("https://s3.ap-northeast-1.wasabisys.com/{$this->bucket}//", $out);

        // Check bucket-giga-d-23-00288 has been created
        $I->runShellCommand('/app/yii_test wasabi-bucket/list-buckets');
        $listBucketsOutput = $I->grabShellOutput();
        $I->assertStringContainsString($this->bucket, $listBucketsOutput);
    }

    /**
     * Test actionCreate function with bucket name containing upper case
     * letters should fail
     *
     * @param FunctionalTester $I
     */
    public function tryCreateBucketWithBucketNameContainingUpperCaseLetters(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test wasabi-bucket/create --bucketName $this->bucketNameWithUpperCaseLetters", false);
        $I->seeResultCodeIs(64);
        $out = $I->grabShellOutput();
        codecept_debug($out);
        $I->assertStringContainsString('cannot contain upper case letters', $out);
    }
}
