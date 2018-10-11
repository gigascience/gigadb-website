<?php

// change the following paths if necessary
$yiit=getenv("YII_PATH").'/framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';
$composer=dirname(__FILE__)."/../../vendor/autoload.php";

require_once($yiit);
require_once($composer);
Yii::$enableIncludePath = false;
Yii::createWebApplication($config);
