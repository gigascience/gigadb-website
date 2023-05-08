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
use yii\helpers\Console;

/**
 * Read file in bucket
 *
 */
class WasabiUserController extends Controller
{
    /**
     * Username for Wasabi account
     *
     * @var string $username
     */
    public $username = '';

    /**
     * Specify options available to console command provided by this controller.
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

    public function init()
    {
        parent::init();
    }

    /**
     * Create user account
     * @return int Exit code
     */
    public function actionCreate()
    {
        $optUserName   = $this->username;

        // Return usage unless mandatory options are passed
        if ($optUserName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-user/create --username theUsername" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            $result = Yii::$app->WasabiUserComponent->create($optUserName);
            if ($result) {
                // Log result output
                Yii::info($result);
                $this->stdout($result->get('User')['Arn'] . PHP_EOL, Console::FG_GREEN);
            }
        } catch (IamException $e) {
            // Handle any IamException bubbled up from WasabiUserComponent
            $this->stdout($e->getMessage() . PHP_EOL, Console::FG_RED);
            // Log error message
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }
}
