<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

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
    public $username = '';

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
        ];
    }

    /**
     * Creates new Wasabi policy for an author to upload data into their own bucket
     * @return int Exit code
     */
    public function actionCreateAuthorPolicy(): int
    {
        $optUserName   = $this->username;
        // Return usage unless mandatory options are passed
        if ($optUserName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-policy/create --username theWasabiUserName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        $policy = Yii::$app->PolicyGenerator->generateAuthorPolicy($optUserName);
        $result = Yii::$app->WasabiPolicyComponent->createAuthorPolicy($optUserName, $policy);
        // Extract policy ARN from AWS Result object
        $arn = $result->get("Policy")["Arn"];
        Yii::$app->WasabiPolicyComponent->attachPolicyToUser($arn, $optUserName);
        return ExitCode::OK;
    }
}
