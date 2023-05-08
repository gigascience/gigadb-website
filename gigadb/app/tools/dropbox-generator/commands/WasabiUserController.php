<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use Exception;
use Aws\Iam\Exception\IamException;

/**
 * Console commands for managing users in Wasabi
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
     *
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

    /**
     * Lists all user accounts
     *
     * @return int Exit code
     */
    public function actionListUsers(): int
    {
        try {
            $result = Yii::$app->WasabiUserComponent->listUsers();
            Yii::info($result);
            $users = $result->get("Users");
            foreach ($users as $user) {
                $this->stdout($user["UserName"] . PHP_EOL, Console::FG_GREEN);
            }
        } catch (IamException $e) {
            $this->stdout($e->getMessage() . PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }

    /**
     * Delete user account given a username
     *
     * @return int Exit code
     */
    public function actionDelete(): int
    {
        $optUserName = $this->username;

        // Return usage unless mandatory options are passed
        if ($optUserName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-user/delete --username theUserName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            $result = Yii::$app->WasabiUserComponent->deleteUser($optUserName);
            Yii::info($result);
            $statusCode = $result->get("@metadata")["statusCode"];
            if ($statusCode != 200) {
                throw new Exception("Delete user did not return HTTP 200 status response code!");
            }
            $this->stdout("User deleted" . PHP_EOL, Console::FG_GREEN);
        } catch (IamException | Exception $e) {
            $this->stdout($e->getMessage() . PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }
}
