<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use \Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use Aws\Iam\Exception\IamException;
use Aws\Iam\IamClient;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Read file in bucket
 *
 */
class WasabiPolicyController extends Controller
{
    /**
     * Template file name
     *
     * @var string $template
     */
    public $template = '';

    /**
     * Wasabi username
     *
     * @var string $username
     */
    public $username = '';

    /**
     * Wasabi group
     *
     * @var string $group
     */
    public $group = '';

    /**
     * Specify options available to console command provided by this controller.
     *
     * @var    $actionID
     * @return array List of controller class's public properties
     */
    public function options($actionID): array
    {
        return [
            'template',
            'username',
            'group',
        ];
    }

    public function init()
    {
        parent::init();
    }

    /**
     * Create new Wasabi policy
     * @return int Exit code
     */
    public function actionCreate()
    {
        $optUserName   = $this->username;
        // Return usage unless mandatory options are passed.
        if ($optUserName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-policy/createpolicy --username theWasabiUserName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        $policy = Yii::$app->PolicyGenerator->generateAuthorPolicy($optUserName);
        $result = Yii::$app->WasabiPolicyComponent->createAuthorPolicy($optUserName, $policy);
        // Extract policy ARN
        $arn = $result->get("Policy")["Arn"];
        $result = Yii::$app->WasabiPolicyComponent->attachPolicyToUser($arn, $optUserName);
        return ExitCode::OK;
    }

    /**
     * Attach policy to a user
     * @return int Exit code
     */
    public function actionAttachToUser()
    {
        $optUsername = $this->username;

        // Return usage unless mandatory options are passed.
        if ($optUsername === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-policy/attachtouser --username theAuthorUserName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        //Establish connection to wasabi
        $iam = new IamClient($this->credentials);

        try {
            $result = $iam->attachUserPolicy(array(
                // UserName is required
                'UserName' => $optUsername,
                // PolicyArn is required
                'PolicyArn' => $policyArn,
            ));
            var_dump($result);
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        return ExitCode::OK;
    }
}
