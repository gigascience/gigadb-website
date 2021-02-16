<?php

namespace console\controllers;

use Yii;
use \yii\helpers\Console;
use \yii\console\Controller;
use common\models\Upload;
use backend\models\FiledropAccount;
use \Docker\Docker;
use backend\models\DockerManager;
use yii\console\ExitCode;

/**
 * Console controller for operating File Upload Wizard House keeping tasks
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FuwController extends Controller {
    /**
     * @var string $doi dataset identifier on which to run housekeeping tasks
     */
    public $doi;

    private $ftpRoot = "incoming/ftp";

    private $filedropRoot = "repo";

    private $credentialsRoot = "private";

    /**
     * Remove the dropbox created for a given Dataset Identifier
     * e.g: ./yii fuw/remove-dropbox --doi 000007
     * - it deletes the dropbox directory and its files
     * - it marks the file upload records as archived if any
     * - it marks the filedrop account record as terminated
     * - it removes the ftp accounts
     *
     * @uses \backend\models\FiledropAccount
     * @uses \backend\models\DockerManager
     *
     * @returns ExitCode::OK | ExitCode::USAGE | ExitCode::DATAERR | ExitCode::OSERR
     */
    public function actionRemoveDropbox() {

        if(!($this->doi)) {
            Yii::error("wrong number of arguments (DOI missing), exiting abnormally");
            $this->stdout("wrong number of arguments (DOI missing), exiting abnormally".PHP_EOL, Console::FG_RED);
            return ExitCode::USAGE;
        }

        try {
            $docker = new DockerManager();
            $docker->setClient(Docker::create());

            $fileDrop = FiledropAccount::findOne(["doi" => $this->doi]);
            if (!$fileDrop) {
                $this->stdout("Filedrop account not found for DOI {$this->doi}, exiting abnormally".PHP_EOL, Console::FG_RED);
                Yii::error("Filedrop account not found for DOI {$this->doi}, exiting abnormally");
                return ExitCode::DATAERR;
            }

            $fileDrop->setDOI($this->doi);
            $fileDrop->setDockerManager($docker);
            $fileDrop->setStatus(FiledropAccount::STATUS_TERMINATED);
            $fileDrop->save();
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }

        return ExitCode::OK;
    }

    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help','doi'];
    }

}