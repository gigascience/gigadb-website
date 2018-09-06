<?php

// change the following paths if necessary
$yiit=getenv("YII_PATH").'/framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';
$behat=dirname(__FILE__)."/../../vendor/autoload.php";

require_once($yiit);
require_once($behat);

Yii::$enableIncludePath = false;
Yii::createWebApplication($config);
