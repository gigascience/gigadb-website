<?php
$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
$db['dsn'] = getenv("DOCKER_RUNNING") ? 'pgsql:host=database;dbname=gigadb;port=5432' : 'pgsql:host=localhost;dbname=gigadb;port=54321';

return $db;
