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
        codecept_debug("Executing _after() function...");
        $result = Yii::$app->WasabiPolicyComponent->listPolicies();
        $policies = $result->get("Policies");
//        codecept_debug($policies);
//        if (in_array("policy-author-giga-d-23-00286", $policies)) {
//            codecept_debug("Found policy to delete");
            Yii::$app->WasabiPolicyComponent->detachUserPolicy("author-giga-d-4-00286", "arn:aws:iam:::policy/policy-author-giga-d-4-00286");
            Yii::$app->WasabiPolicyComponent->deletePolicy("arn:aws:iam:::policy/policy-author-giga-d-4-00286");
//        }
    }

    /**
     * Test actionCreateAuthorPolicy() in WasabiPolicyController
     *
     * This test will fail if the policy is already present in Wasabi.
     *
     * @param FunctionalTester $I
     */
    public function tryCreateAuthorPolicy(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test wasabi-policy/create-author-policy --username author-giga-d-4-00286");
        // Now check for existence of the policy created by above command
        $result = Yii::$app->WasabiPolicyComponent->listPolicies();
        $policies = $result->get("Policies");
//         codecept_debug($policies);
        // If a key is found then this means that the policy was created
        $key = array_search("policy-author-giga-d-4-00286", array_column($policies, 'PolicyName'));
        $I->assertNotFalse($key);
    }
}
