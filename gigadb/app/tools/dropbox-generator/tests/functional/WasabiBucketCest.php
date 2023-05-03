<?php

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class WasabiBucketCest
{
    /**
     * For storing credentials to access Wasabi
     *
     * @var [] $credentials
     */
    public $credentials = [];

    /**
     * Setup code that is run before each test
     *
     * @return void
     */
    public function _before()
    {
        $this->credentials = array(
            'credentials' => [
                'key' => Yii::$app->params['wasabi']['key'],
                'secret' => Yii::$app->params['wasabi']['secret']
            ],
            'endpoint' => Yii::$app->params['wasabi']['bucket_endpoint'],
            'region' => Yii::$app->params['wasabi']['bucket_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
        );
    }

    /**
     * Teardown code that is run after each test
     *
     * Currently just removes the Wasabi user that was created by this test
     *
     * @return void
     */
    public function _after()
    {
        $buckets = $this->listBuckets();
        if (in_array("bucket-giga-d-23-00288", $buckets)) {
            codecept_debug("Found bucket to delete");
            $this->deleteBucket("bucket-giga-d-23-00288");
        }
    }

    /**
     * Test actionCreate function in WasabiBucketController
     *
     * @param FunctionalTester $I
     */
    public function tryCreateBucket(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test wasabi-bucket/create --bucketName bucket-giga-d-23-00288");
        # If above console command is successful then we should see the bucket name in output
        $I->seeInShellOutput("bucket-giga-d-23-00288");
    }

    private function listBuckets()
    {
        //Establish Wasabi connection
        $s3Client = new S3Client($this->credentials);

        $bucketNames = array();
        try {
            $result = $s3Client->listBuckets();
            $buckets = $result->get("Buckets");
            codecept_debug($buckets);
            foreach ($buckets as $bucket) {
                $bucketNames[] = $bucket["Name"];
            }
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        return $bucketNames;
    }

    private function deleteBucket($bucketName)
    {
        $s3Client = new S3Client($this->credentials);

        try {
            $s3Client->deleteBucket([
                'Bucket' => "$bucketName"
            ]);
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}
