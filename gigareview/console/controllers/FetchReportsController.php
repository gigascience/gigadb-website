<?php

namespace console\controllers;

use Yii;
use \yii\helpers\Console;
use \yii\console\Controller;
use yii\console\ExitCode;

/**
 * Console controller that fetch editorial manager reports
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FetchReportsController extends Controller
{

    public function actionFetch(): int
    {
        return ExitCode::OK;
    }

    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help'];
    }

}