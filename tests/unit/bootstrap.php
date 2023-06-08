<?php

// change the following paths if necessary
$yiit = __DIR__ . '/../../protected/yiit.php';
$config = dirname(__FILE__) . '/../../protected/config/test.php';
$composer = dirname(__FILE__) . "/../../vendor/autoload.php";

require_once($yiit);
require_once($composer);

# Load the modified root class for Yii1.1/Yii2.0
$yii = dirname(__FILE__) . '/../../protected/components/Yii.php';
require_once($yii);

# Load and run Yii web application
Yii::$enableIncludePath = false;
Yii::createWebApplication($config);

# load Yii 2 (but don't run the web application)
$yii2Config = require(dirname(__FILE__) . '/../../protected/config/yii2/test.php');
new yii\web\Application($yii2Config);
