<?php

$testDB = $params['db']['database']."_test";
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => "pgsql:host={$params['db']['host']};dbname=$testDB;port={$params['db']['port']}",
            'username' => $params['db']['username'],
            'password' => $params['db']['password'],
            'charset' => 'utf8',
        ],
    ],
];
