<?php

class WasabiPolicyCest
{
    /**
     * Teardown code that is run after each test
     *
     * Currently just removes the Wasabi user that was created by this test
     *
     * @return void
     */
    public function _after()
    {
        $result = Yii::$app->WasabiPolicyComponent->listPolicies();
        $policies = $result->get("Policies");
        $key = array_search("policy-author-giga-d-4-00286", array_column($policies, 'PolicyName'));
        if ($key !== false) {
            Yii::$app->WasabiPolicyComponent->detachUserPolicy("author-giga-d-4-00286", "arn:aws:iam:::policy/policy-author-giga-d-4-00286");
            $arn = $policies[$key]["Arn"];
            Yii::$app->WasabiPolicyComponent->deletePolicy($arn);
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
        $I->runShellCommand("/app/yii_test wasabi-policy/create-author-policy --username author-giga-d-4-00286");
        // Check existence of policy created by above command
        $result = Yii::$app->WasabiPolicyComponent->listPolicies();
        $policies = $result->get("Policies");
        codecept_debug($policies);
        // Policy was created if key found
        $key = array_search("policy-author-giga-d-4-00286", array_column($policies, 'PolicyName'));
        $I->assertNotFalse($key);
    }
}
