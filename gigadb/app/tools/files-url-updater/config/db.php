<?php

$params = require __DIR__ . '/params.php';

return [
    'class' => 'yii\db\Connection',
    'dsn' => "pgsql:host={$params['db']['host']};dbname={$params['db']['database']};port={$params['db']['port']}",
    'username' => $params['db']['username'],
    'password' => $params['db']['password'],
    'attributes' => [
        "host" => $params['db']['host'],
        "port" => $params['db']['port'],
        "database" => $params['db']['database'],
        "test_database" => $params['db']['test_database'],
        ],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
