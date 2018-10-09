<?php

// change the following paths if necessary
$yiit=getenv("YII_PATH").'/framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';
$composer=dirname(__FILE__)."/../../vendor/autoload.php";

require_once($yiit);
require_once($composer);
require_once(dirname(__FILE__)."/support/BrowserSignInSteps.php");
require_once(dirname(__FILE__)."/support/BrowserPageSteps.php");
require_once(dirname(__FILE__)."/support/BrowserFormSteps.php");
require_once(dirname(__FILE__)."/support/BrowserFindSteps.php");
require_once(dirname(__FILE__)."/support/FunctionalTesting.php");
require_once(dirname(__FILE__)."/support/CommonDataProviders.php");
Yii::$enableIncludePath = false;
Yii::createWebApplication($config);
