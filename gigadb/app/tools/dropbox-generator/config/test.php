<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
        'PolicyGenerator' => [
            'class' => 'app\components\PolicyGenerator',
        ],
        'WasabiPolicyComponent' => [
            'class' => 'app\components\WasabiPolicyComponent',
        ],
        'WasabiBucketComponent' => [
            'class' => 'app\components\WasabiBucketComponent',
        ],
        'WasabiUserComponent' => [
            'class' => 'app\components\WasabiUserComponent',
        ],
        'db' => $db,
    ],
    'params' => $params,
];
