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
     * For storing credentials to access Wasabi
     *
     * @var [] $credentials
     */
    public $credentials = [];

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

        $policy = Yii::$app->PolicyGenerator->createAuthorPolicy($optUserName);
        echo $policy;

        // Create policy in Wasabi
//        $iam = new IamClient($this->credentials);
//        try {
//            $result = $iam->createPolicy(array(
//                // PolicyName is required
//                'PolicyName' => 'myDynamoDBPolicy',
//                // PolicyDocument is required
//                'PolicyDocument' => $myManagedPolicy
//            ));
//            var_dump($result);
//        } catch (IamException $e) {
//            echo $e->getMessage() . PHP_EOL;
//        }

        return ExitCode::OK;
    }

    public function actionAttachToUser()
    {
        $optGroup   = $this->group;

        // Return usage unless mandatory options are passed.
        if ($optGroup === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-policy/attachtogroup --group theGroupName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        //Establish connection to wasabi
        $iam = new IamClient($this->credentials);

        try {
            $result = $iam->attachUserPolicy(array(
                // UserName is required
                'UserName' => $userName,
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
