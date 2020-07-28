<?php

namespace console\controllers;

use Yii;
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
    	$this->stdout("DOCKER_HOST: ".getenv("DOCKER_HOST").PHP_EOL,Console::FG_RED);
        $manager = new DockerManager();
        $manager->setClient(Docker::create());
        try {
	        $this->stdout($manager->getContainer("/ftpd_1/")->getNames()[0].PHP_EOL, Console::BOLD);
        }
        catch(Exception $e) {
        	$this->stdout("remote_docker_hostname: ".Yii::$app->params['remote_docker_hostname'].PHP_EOL);
            $this->stdout("DOCKER_HOST: ".getenv("DOCKER_HOST").PHP_EOL);
        	$this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
        }
    }

    /** 
    * Check tusd is configured and up and running
    *
    * i.e: GET on https://<gigadb url>/fileserver should return "405 Method not allowed" 
    */
    public function actionCheckTusdEndpoint() 
    {
        if ( in_array( YII_ENV, ["dev","CI"]) ) {
            $tusdFullUrl = "http://gigadb.dev/fileserver" ;
        }
        else {
            $tusdFullUrl = "https://".Yii::$app->params["dataset_filedrop"]['tusd_host']."/fileserver";
        }
        $response = system("curl -sL $tusdFullUrl");
        assert("Method Not Allowed" == $response, "Should return 'Method Not Allowed' for $tusdFullUrl on ". YII_ENV);
    }



}
