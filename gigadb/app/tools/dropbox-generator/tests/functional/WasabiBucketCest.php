<?php

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class WasabiBucketCest
{
    /**
     * Teardown code that is run after each test
     *
     * Currently just removes the Wasabi user that was created by this test
     *
     * @return void
     */
    public function _after(FunctionalTester $I)
    {
        // Delete bucket created in tryCreateBucket() function
        $I->runShellCommand("/app/yii_test wasabi-bucket/delete --bucketName bucket-giga-d-23-00288");
        $I->seeResultCodeIs(0);
    }

    /**
     * Test actionCreate function in WasabiBucketController
     *
     * @param FunctionalTester $I
     */
    public function tryCreateBucket(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test wasabi-bucket/create --bucketName bucket-giga-d-23-00288");
        $I->seeResultCodeIs(0);
        $out = $I->grabShellOutput();
        $I->assertStringContainsString('https://s3.ap-northeast-1.wasabisys.com/bucket-giga-d-23-00288//', $out);

        // Check bucket-giga-d-23-00288 has been created
        $I->runShellCommand('/app/yii_test wasabi-bucket/list-buckets');
        $listBucketsOutput = $I->grabShellOutput();
        $I->assertStringContainsString('bucket-giga-d-23-00288', $listBucketsOutput);
    }
}
