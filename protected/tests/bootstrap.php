<?php

// change the following paths if necessary
$yiit=__DIR__.'/../yiit.php';
$config=dirname(__FILE__).'/../config/test.php';
$composer=dirname(__FILE__)."/../../vendor/autoload.php";

require_once($yiit);
require_once($composer);
Yii::$enableIncludePath = false;
Yii::createWebApplication($config);

# load Yii 2 (but don't run the web application)
$yii2Config = require(dirname(__FILE__).'/../config/yii2/test.php');
new yii\web\Application($yii2Config);
