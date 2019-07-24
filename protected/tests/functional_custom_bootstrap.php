<?php

// PHPUnit needs to now where to find classes and library
$yiit=__DIR__.'/../yiit.php';
$config=dirname(__FILE__).'/../config/test.php';
$composer=dirname(__FILE__)."/../../vendor/autoload.php";

require_once($yiit);
require_once($composer);
Yii::$enableIncludePath = false;
Yii::createWebApplication($config);

// Before hooks for our functional tests
print_r("Loading environment variables... ".PHP_EOL);
$dotenv = Dotenv\Dotenv::create('/var/www', '.secrets');
$dotenv->load();
print_r("Loading database config...".PHP_EOL);
$dbconf = json_decode(file_get_contents(dirname(__FILE__).'/../config/db.json'), true);
$db_host = $dbconf["host"];
$db_name = $dbconf["database"];
$db_user = $dbconf["user"];
$db_password = $dbconf["password"];
print_r("database config (host, db name, user): $db_host, $db_name, $db_user.".PHP_EOL);
print_r("Backing up current database...".PHP_EOL);
exec("pg_dump $db_name -U $db_user -h $db_host -F custom  -f /var/www/sql/before-run.pgdmp 2>&1",$output);
print_r("Loading test database... ".PHP_EOL);
GigadbWebsiteContext::call_pg_terminate_backend($db_name);
GigadbWebsiteContext::recreateDB($db_name);
exec("pg_restore -h $db_host -U $db_user -d $db_name --clean --no-owner -v /var/www/sql/gigadb_testdata.pgdmp || true 2>&1",$output);
GigadbWebsiteContext::containerRestart();

// After hooks for our functional tests
register_shutdown_function(function(){
   	print_r("Restoring current database...".PHP_EOL);
   	GigadbWebsiteContext::call_pg_terminate_backend($GLOBALS['db_name']);
   	GigadbWebsiteContext::recreateDB($GLOBALS['db_name']);
    exec("pg_restore -h $db_host  -U $db_user -d $db_name --clean --no-owner -v /var/www/sql/before-run.pgdmp 2>&1",$output);
   	GigadbWebsiteContext::containerRestart();
});