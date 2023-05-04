<?php

namespace app\components;

use Exception;
use GigaDB\models\Dataset;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Component;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Aws\Iam\Exception\IamException;
use Aws\Iam\IamClient;
use \Yii;

/**
 * Component service to output Wasabi policies
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
            // 'debug'   => true
        );
    }


    /**
     * Create a policy for an author to access their bucket in Wasabi
     *
     * @param string $authorUserName Wasabi username of the author.
     *
     * @return string Contents of policy
     */
    public function createAuthorPolicy(string $authorUserName, string $policy)
    {
        $iam = new IamClient($this->credentials);
        try {
            $result = $iam->createPolicy([
                'PolicyName' => 'policy-' . "$authorUserName",
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
     * @param string $policyArn Amazon Resource Name for policy.
     *
     * @return string Contents of policy
     */
    public function attachPolicyToUser(string $policyArn, $username)
    {
        $iam = new IamClient($this->credentials);
        try {
            $result = $iam->attachUserPolicy([
                'UserName' => $username,
                'PolicyArn' => $policyArn,
            ]);
            var_dump($result);
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        return $result;
    }
}
