<?php

namespace app\components;

use Yii;
use yii\base\Component;
use Aws\Iam\Exception\IamException;
use Aws\Iam\IamClient;
use Aws\Result;

/**
 * Component class for creating and attaching policies in Wasabi
 */
class WasabiPolicyComponent extends Component
{
    /**
     * For storing credentials to access Wasabi
     *
     * @var [] $credentials
     */
    public $credentials = [];

    /**
     * Initialize component
     */
    public function init()
    {
        parent::init();
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
     * Create a policy in Wasabi that restricts an author to their own bucket
     *
     * @param string $authorUserName Wasabi username of the author.
     * @return Result AWS result object
     */
    public function createAuthorPolicy(string $authorUserName, string $policy): Result
    {
        $iam = new IamClient($this->credentials);
        try {
            $result = $iam->createPolicy([
                'PolicyName'     => 'policy-' . "$authorUserName",
                'PolicyDocument' => $policy
            ]);
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        return $result;
    }

    /**
     * Attaches a policy to a user in Wasabi
     *
     * @param string $policyArn Amazon Resource Name for policy
     * @return Result AWS result object
     */
    public function attachPolicyToUser(string $policyArn, $username)
    {
        $iam = new IamClient($this->credentials);
        try {
            $result = $iam->attachUserPolicy([
                'UserName' => $username,
                'PolicyArn' => $policyArn,
            ]);
        } catch (IamException $e) {
            echo "Problem interacting with AWS IAM service: " . $e->getMessage() . PHP_EOL;
        }
        return $result;
    }

    /**
     * Returns an array containing policy names
     *
     * Currently only used by WasabiPolicyCest class.
     *
     * @return array
     */
    public function listPolicies(): \Aws\Result
    {
        $iamClient = new IamClient($this->credentials);
        try {
            $result = $iamClient->listPolicies();
            $policies = $result->get("Policies");
//            foreach ($policies as $policy) {
//                $policyNames[] = $policy["Name"];
//            }
        } catch (IamException $e) {
            echo "Problem interacting with Wasabi IAM service: " . $e->getMessage() . PHP_EOL;
        }
        return $result;
    }

    public function detachUserPolicy($UserName, $PolicyArn)
    {
        $iamClient = new IamClient($this->credentials);
        try {
            $result = $iamClient->detachUserPolicy(array(
                'UserName'  => "$UserName",
                'PolicyArn' => "$PolicyArn",
            ));
            var_dump($result);
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        return $result;
    }

    public function deletePolicy($policyArn)
    {
        $iamClient = new IamClient($this->credentials);
        try {
            $result = $iamClient->deletePolicy([
                'PolicyArn' => "$policyArn"
            ]);
//            var_dump($result);
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        return $result;
    }
}
