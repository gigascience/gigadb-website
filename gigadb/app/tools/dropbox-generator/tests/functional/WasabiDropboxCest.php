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

    public string $expectedAuthorUserName = 'author-giga-d-23-00288';

    public string $expectedBucketName = 'bucket-giga-d-23-00288';

    public string $differentBucket = 'bucket-giga-d-23-00123';

    public string $policyArn = '';

    public string $accessKey = '';

    public string $accessSecret = '';

    /**
     * Teardown code that is executed after dropbox workflow
     *
     * Removes user account, user policy and bucket after functional test has
     * executed.
     *
     * @return void
     */
    public function _after()
    {
        try {
            // Delete test file in bucket
            Yii::$app->WasabiBucketComponent->deleteObject(
                $this->expectedBucketName,
                'README.md',
                $this->accessKey,
                $this->accessSecret
            );
            // Delete bucket
            Yii::$app->WasabiBucketComponent->deleteBucket('bucket-giga-d-23-00288');
            // Detach policy from user
            Yii::$app->WasabiPolicyComponent->detachUserPolicy('author-giga-d-23-00288', $this->policyArn);
            // Delete policy
            Yii::$app->WasabiPolicyComponent->deletePolicy($this->policyArn);
            // Delete user's access keys
            Yii::$app->WasabiUserComponent->deleteAccessKey($this->accessKey, 'author-giga-d-23-00288');
            // Delete user
            Yii::$app->WasabiUserComponent->deleteUser('author-giga-d-23-00288');
        } catch (IamException | S3Exception | Exception $e) {
            print_r("Caught exception: " . $e->getMessage() . PHP_EOL);
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
        $authorUserName = "author-{$this->manuscriptId}";
        $I->runShellCommand("/app/yii_test wasabi-user/create --username {$authorUserName}");
        $I->seeResultCodeIs(0);
        $userArn = $I->grabShellOutput();
        $I->assertStringContainsString("user/{$this->expectedAuthorUserName}", $userArn);

        // Check author user account has been created in Wasabi
        $I->runShellCommand('/app/yii_test wasabi-user/list-users');
        $listUsersOutput = $I->grabShellOutput();
        $I->assertStringContainsString($this->expectedAuthorUserName, $listUsersOutput);

        // Create access key for author user
        $I->runShellCommand("/app/yii_test wasabi-user/create-access-key --username {$this->expectedAuthorUserName}");
        $I->seeResultCodeIs(0);
        $keyAndSecret = $I->grabShellOutput();
        codecept_debug($keyAndSecret);
        // Separate credentials into 2 strings
        $tokens = explode("\n", $keyAndSecret);
        $this->accessKey = str_replace('key=', '', $tokens[0]);
        $this->accessSecret = str_replace('secret=', '', $tokens[1]);

        // Create bucket name for author dropbox
        $bucketName = 'bucket-' . $this->manuscriptId;
        // Create bucket using bucket name
        $I->runShellCommand("/app/yii_test wasabi-bucket/create --bucketName {$bucketName}");
        $I->seeResultCodeIs(0);
        $bucketLocation = $I->grabShellOutput();
        $I->assertStringContainsString("https://s3.ap-northeast-1.wasabisys.com/{$this->expectedBucketName}//", $bucketLocation);
        // Check author's bucket has been created
        $I->runShellCommand('/app/yii_test wasabi-bucket/list-buckets');
        $listBucketsOutput = $I->grabShellOutput();
        $I->assertStringContainsString($this->expectedBucketName, $listBucketsOutput);

        // Create user policy for author
        $I->runShellCommand("/app/yii_test wasabi-policy/create-author-policy --username {$authorUserName}");
        $this->policyArn = $I->grabShellOutput();
        // Check policy has been created
        $awsResult = Yii::$app->WasabiPolicyComponent->listPolicies();
        $policies = $awsResult->get("Policies");
        // Policy was created if key found
        $index = array_search("policy-{$this->expectedAuthorUserName}", array_column($policies, 'PolicyName'));
        $I->assertNotFalse($index);

        // Attach policy to user
        $I->runShellCommand("/app/yii_test wasabi-policy/attach-to-user --username {$this->expectedAuthorUserName} --policy-arn {$this->policyArn}");
        // Now author user can upload data into bucket dropbox
        $I->runShellCommand("/app/yii_test wasabi-bucket/put-object --bucket-name {$this->expectedBucketName} --key README.md --file-path /app/README.md --access-key {$this->accessKey} --access-secret {$this->accessSecret}");
        // Check author user cannot upload data into a different bucket
        $I->runShellCommand("/app/yii_test wasabi-bucket/put-object --bucket-name {$this->differentBucket} --key README.md --file-path /app/README.md --access-key {$this->accessKey} --access-secret {$this->accessSecret}", false);
        $putObjectStatus = $I->grabShellOutput();
        $I->assertStringContainsString('Access Denied', $putObjectStatus);
    }
}
