<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => "pgsql:host={$params['db']['host']};dbname={$params['db']['database']};port={$params['db']['port']}",
            'username' => $params['db']['username'],
            'password' => $params['db']['password'],
            'charset' => 'utf8',
        ],
    ],
];
