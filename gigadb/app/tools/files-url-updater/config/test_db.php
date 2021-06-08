<?php
$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
$db['dsn'] = "pgsql:host={$params['db']['host']};dbname={$params['db']['test_database']};port={$params['db']['port']}";
$db['attributes']['database'] = 'test_database';

return $db;
