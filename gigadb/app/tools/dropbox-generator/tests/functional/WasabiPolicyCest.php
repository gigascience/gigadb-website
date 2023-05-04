<?php

use Aws\Iam\IamClient;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class WasabiPolicyCest
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
            'endpoint' => Yii::$app->params['wasabi']['iam_endpoint'],
            'region' => Yii::$app->params['wasabi']['iam_region'],
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
     * Test actionCreate function in WasabiPolicyController
     *
     * @param FunctionalTester $I
     */
    public function tryCreatePolicy(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test wasabi-policy/create --username author-giga-d-4-00286");
        # We should see bucket name in output \AWS\Result object
        $I->seeInShellOutput("bucket-giga-d-23-00288");
    }

    
}
