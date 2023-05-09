<?php

namespace app\tests\functional;

use FunctionalTester;
use Yii;

/**
 * Class containing functional tests for WasabiPolicyController
 */
class WasabiPolicyCest
{
    /*
     * @var string username for author to use in tests
     */
    public string $authorUserName = 'author-giga-d-4-00286';

    /**
     * Teardown code that is executed after every test
     *
     * Currently just detaches and deletes a policy created by
     * tryCreateAuthorPolicy() function.
     *
     * @return void
     */
    public function _after()
    {
        $policyName = "policy-{$this->authorUserName}";
        $result = Yii::$app->WasabiPolicyComponent->listPolicies();
        $policies = $result->get("Policies");
        $key = array_search($policyName, array_column($policies, 'PolicyName'));
        if ($key !== false) {
            Yii::$app->WasabiPolicyComponent->detachUserPolicy(
                $this->authorUserName,
                "arn:aws:iam:::policy/{$policyName}"
            );
            $policyArn = $policies[$key]["Arn"];
            Yii::$app->WasabiPolicyComponent->deletePolicy($policyArn);
        }
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
        $policyName = "policy-{$this->authorUserName}";

        $I->runShellCommand("/app/yii_test wasabi-policy/create-author-policy --username {$this->authorUserName}");
        // Check existence of policy created by above command
        $result = Yii::$app->WasabiPolicyComponent->listPolicies();
        $policies = $result->get("Policies");
        codecept_debug($policies);
        // Policy was created if key found
        $key = array_search($policyName, array_column($policies, 'PolicyName'));
        $I->assertNotFalse($key);
    }
}
