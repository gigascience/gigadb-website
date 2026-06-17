<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../protected/yiit.php';

$config = dirname(__FILE__) . '/../../protected/config/test.php';

# Load the modified root class for Yii1.1/Yii2.0
$yii = dirname(__FILE__) . '/../../protected/components/Yii.php';
require_once($yii);

# Load and run Yii web application
Yii::$enableIncludePath = false;
Yii::createWebApplication($config);

# load Yii 2 (but don't run the web application)
$yii2Config = require(dirname(__FILE__) . '/../../protected/config/yii2/test.php');
new yii\web\Application($yii2Config);


// Before hooks for our functional tests
print_r('Loading environment variables... ' . PHP_EOL);
$dotenv = Dotenv\Dotenv::create('/var/www', '.env');
$dotenv->load();
$secrets = Dotenv\Dotenv::create('/var/www', '.secrets');
$secrets->overload();
print_r('Loading database config...' . PHP_EOL);

shell_exec("./protected/yiic migrate to 300000_000000 --connectionID=db --migrationPath=application.migrations.admin --interactive=0");
shell_exec("./protected/yiic migrate mark 000000_000000 --connectionID=db --interactive=0");
shell_exec("./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.schema --interactive=0");
shell_exec("./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.data.dev --interactive=0");
shell_exec("./protected/yiic migrate --connectionID=db --migrationPath=application.migrations.fix_import --interactive=0");
shell_exec("./protected/yiic sequencefixer fixAll");
shell_exec("./protected/yiic custommigrations refreshmaterializedviews");
shell_exec("./protected/yiic configchange searchresult --limit=2");
