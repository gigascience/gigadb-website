<?php

namespace app\tests\functional;

use Aws\Iam\Exception\IamException;
use Aws\S3\Exception\S3Exception;
use Exception;
use FunctionalTester;
use Yii;

/**
 * Class containing functional test to create an author dropbox in Wasabi
 */
class WasabiDropboxCest
{
    public string $manuscriptId = 'giga-d-23-00288';

    public string $policyArn = '';

    public string $accessKey = '';

    public string $accessSecret = '';

    /**
     * Teardown code that is executed after dropbox workflow test
     *
     * @return void
     */
    public function _after()
    {
        try {
            // Delete test file in bucket
            $result = Yii::$app->WasabiBucketComponent->deleteObject('bucket-giga-d-23-00288', 'README.md', $this->accessKey, $this->accessSecret);
            // Delete bucket
            $result = Yii::$app->WasabiBucketComponent->deleteBucket('bucket-giga-d-23-00288');
            // Detach policy from user
            $result = Yii::$app->WasabiPolicyComponent->detachUserPolicy('author-giga-d-23-00288', $this->policyArn);
            // Delete policy
            $result = Yii::$app->WasabiPolicyComponent->deletePolicy($this->policyArn);
            // Delete user's access keys
            $result = Yii::$app->WasabiUserComponent->deleteAccessKey($this->accessKey, 'author-giga-d-23-00288');
            // Delete user
            $result = Yii::$app->WasabiUserComponent->deleteUser('author-giga-d-23-00288');
        } catch (IamException | S3Exception | Exception $e) {
            print_r("Caught exception: " . $e->getMessage() . PHP_EOL);
            $this->stdout($e->getMessage() . PHP_EOL, Console::FG_RED);
        }
    }

    /**
     * Test multiple controller functions required to create an author dropbox
     * in Wasabi
     *
     * @param FunctionalTester $I
     */
    public function tryCreateAuthorDropbox(FunctionalTester $I)
    {
        // Create Wasabi user account with username based on manuscript identifier
        $authorUserName = 'author-' . $this->manuscriptId;
        $I->runShellCommand("/app/yii_test wasabi-user/create --username $authorUserName");
        $I->seeResultCodeIs(0);
        $out = $I->grabShellOutput();
        $I->assertStringContainsString("user/author-giga-d-23-00288", $out);
        // Check author-giga-d-23-00288 user account has been created
        $I->runShellCommand('/app/yii_test wasabi-user/list-users');
        $listUsersOutput = $I->grabShellOutput();
        $I->assertStringContainsString('author-giga-d-23-00288', $listUsersOutput);
        // Create access key for user
        $I->runShellCommand("/app/yii_test wasabi-user/create-access-key --username author-giga-d-23-00288");
        $I->seeResultCodeIs(0);
        $keyAndSecret = $I->grabShellOutput();
        codecept_debug($keyAndSecret);
        // Parse credentials
        $tokens = explode("\n", $keyAndSecret);
        $this->accessKey = str_replace('key=', '', $tokens[0]);
        $this->accessSecret = str_replace('secret=', '', $tokens[1]);

        // Create bucket name for author dropbox
        $bucketName = 'bucket-' . $this->manuscriptId;
        // Create bucket using bucket name
        $I->runShellCommand("/app/yii_test wasabi-bucket/create --bucketName $bucketName");
        $I->seeResultCodeIs(0);
        $out = $I->grabShellOutput();
        $I->assertStringContainsString('https://s3.ap-northeast-1.wasabisys.com/bucket-giga-d-23-00288//', $out);
        // Check bucket-giga-d-23-00288 has been created
        $I->runShellCommand('/app/yii_test wasabi-bucket/list-buckets');
        $listBucketsOutput = $I->grabShellOutput();
        $I->assertStringContainsString('bucket-giga-d-23-00288', $listBucketsOutput);

        // Create policy
        $I->runShellCommand("/app/yii_test wasabi-policy/create-author-policy --username $authorUserName");
        $this->policyArn = $I->grabShellOutput();
        // Check policy has been created
        $result = Yii::$app->WasabiPolicyComponent->listPolicies();
        $policies = $result->get("Policies");
        // Policy was created if key found
        $key = array_search('policy-' . $authorUserName, array_column($policies, 'PolicyName'));
        $I->assertNotFalse($key);
        // Attach policy to user
        $I->runShellCommand("/app/yii_test wasabi-policy/attach-to-user --username $authorUserName --policy-arn $this->policyArn");

        // Test user can upload data into bucket dropbox
        $I->runShellCommand("/app/yii_test wasabi-bucket/put-object --bucket-name $bucketName --key README.md --file-path /app/README.md --access-key $this->accessKey --access-secret $this->accessSecret");
    }
}
