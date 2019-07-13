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
print_r("Backing up current database...".PHP_EOL);
exec("pg_dump gigadb -U gigadb -h database -F custom  -f /var/www/sql/before-run.pgdmp 2>&1",$output);
print_r("Loading test database... ".PHP_EOL);
exec("pg_restore -h database -U gigadb -d gigadb --clean --no-owner -v /var/www/sql/gigadb_testdata.pgdmp || true 2>&1",$output);
print_r("Loading environment variables... ".PHP_EOL);
$dotenv = Dotenv\Dotenv::create('/var/www', '.secrets');
$dotenv->load();

// After hooks for our functional tests
register_shutdown_function(function(){
   	print_r("Restoring current database...".PHP_EOL);
    exec("pg_restore -h database  -U gigadb -d gigadb --clean --no-owner -v /var/www/sql/before-run.pgdmp 2>&1",$output);
});