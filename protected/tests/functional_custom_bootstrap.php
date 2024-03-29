<?php

// PHPUnit needs to now where to find classes and library
$yiit=__DIR__.'/../yiit.php';
$config=dirname(__FILE__).'/../config/main.php';
$composer=dirname(__FILE__)."/../../vendor/autoload.php";

require_once($yiit);
require_once($composer);

# Load the modified root class for Yii1.1/Yii2.0
$yii=dirname(__FILE__).'/../components/Yii.php';
require_once($yii);

# load Yii 2 (but don't run the web application)
$yii2Config = require(__DIR__ . '/../config/yii2/test.php');
new yii\web\Application($yii2Config);


Yii::$enableIncludePath = false;
Yii::createWebApplication($config);

// Before hooks for our functional tests
print_r("Loading environment variables... ".PHP_EOL);
$dotenv = Dotenv\Dotenv::create('/var/www', '.env');
$dotenv->load();
$secrets = Dotenv\Dotenv::create('/var/www', '.secrets');
$secrets->overload();
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
// GigadbWebsiteContext::call_pg_terminate_backend($db_name);
// GigadbWebsiteContext::recreateDB($db_name);
shell_exec("psql -h $db_host -U $db_user -d $db_name -c 'drop materialized view file_finder'");
shell_exec("psql -h $db_host -U $db_user -d $db_name -c 'drop materialized view sample_finder'");
shell_exec("psql -h $db_host -U $db_user -d $db_name -c 'drop materialized view dataset_finder'");
print_r("pg_restore -h $db_host -U $db_user -d $db_name --clean --no-owner -v /var/www/sql/gigadb.pgdmp 2>&1");
exec("pg_restore -h $db_host -U $db_user -d $db_name --clean --no-owner -v /var/www/sql/gigadb.pgdmp 2>&1",$output);
print_r($output);
shell_exec("psql -h $db_host -U $db_user -d $db_name < /var/www/sql/file_finder.sql");
shell_exec("psql -h $db_host -U $db_user -d $db_name < /var/www/sql/sample_finder.sql");
shell_exec("psql -h $db_host -U $db_user -d $db_name < /var/www/sql/dataset_finder.sql");

GigadbWebsiteContext::containerRestart();

// After hooks for our functional tests
register_shutdown_function(function(array $db){
   	// GigadbWebsiteContext::call_pg_terminate_backend($db['database']);
   	// GigadbWebsiteContext::recreateDB($db['database']);
   	print_r("Restoring current database...".PHP_EOL);
//    exec("pg_restore -h {$db['host']}  -U {$db['user']} -d {$db['database']} --clean --no-owner -v /var/www/sql/before-run.pgdmp 2>&1",$output);
//   	GigadbWebsiteContext::containerRestart();
}, $dbconf);