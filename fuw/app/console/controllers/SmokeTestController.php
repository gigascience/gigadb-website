<?php

namespace console\controllers;

use \yii\helpers\Console;
use \Docker\Docker;
use backend\models\DockerManager;

/**
 * Check configuration and wiring
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class SmokeTestController extends \yii\console\Controller
{
	/** 
	* Check php can connect to Docker API
	*/
    public function actionCheckDockerPhp()
    {
        $manager = new DockerManager();
        $manager->setClient(Docker::create());

        $this->stdout($manager->getContainer("/ftpd_1/")->getNames()[0].PHP_EOL, Console::BOLD);
    }

}
