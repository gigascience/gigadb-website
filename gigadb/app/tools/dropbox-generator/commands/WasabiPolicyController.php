<?php

namespace app\commands;

use Aws\Iam\Exception\IamException;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Controller class for creating policies in Wasabi
 */
class WasabiPolicyController extends Controller
{
    /**
     * Wasabi username
     *
     * @var string $username
     */
    public string $username = '';

    /**
     * Amazon Resource Number for policy
     *
     * @var string $policyArn
     */
    public string $policyArn = '';

    /**
     * Specify options available to console command provided by this controller
     *
     * @var    $actionID
     * @return array List of controller class's public properties
     */
    public function options($actionID): array
    {
        return [
            'username',
            'policyArn'
        ];
    }

    /**
     * Creates new Wasabi policy for an author to upload data into their own bucket
     * @return int Exit code
     */
    public function actionCreateAuthorPolicy(): int
    {
        $optUserName = $this->username;
        // Return usage unless mandatory options are passed
        if ($optUserName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-policy/create --username theWasabiUserName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            $policyContent = Yii::$app->PolicyGenerator->generateAuthorPolicy($optUserName);
            $result = Yii::$app->WasabiPolicyComponent->createAuthorPolicy($optUserName, $policyContent);
            $policyResult = $result->get("Policy");
            $this->stdout($policyResult["Arn"] . PHP_EOL, Console::FG_GREEN);
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
            return ExitCode::OK;
    }

    /**
     *
     * Calls attachPolicyToUser() in WasabiPolicyComponent class
     * @return int Exit code
     */
    public function actionAttachToUser(): int
    {
        $optUserName  = $this->username;
        $optPolicyArn = $this->policyArn;
        // Return usage unless mandatory options are passed
        if ($optUserName === '' || $optPolicyArn === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-policy/attach-to-user --username theWasabiUserName --policyArn thePolicyArn" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            $result = Yii::$app->WasabiPolicyComponent->attachPolicyToUser($optPolicyArn, $optUserName);
            Yii::info($result);
            $statusCode = $result->get("@metadata")["statusCode"];
            if ($statusCode != 200) {
                throw new Exception("Attach policy to user did not return HTTP 200 status response code!");
            }
            $this->stdout("Policy attached to user" . PHP_EOL, Console::FG_GREEN);
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        return ExitCode::OK;
    }
}
