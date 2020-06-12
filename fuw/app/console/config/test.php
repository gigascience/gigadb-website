<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','queue','updateGigaDBqueue'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@gigadb-data' => '/var',
        '@uploads' => 'repo',
        '@publicftp'   => 'ftp/public',        
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => [
                'zhuravljov\yii\queue\monitor\migrations',
            ],
        ],          

    ],
    'components' => [
        'fs' => [
            'class' => 'creocoder\flysystem\LocalFilesystem',
            'path' => '/app/console/tests/_output',
        ],     
        'queue' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,         
            'host' => 'beanstalkd',
            'port' => 11300,
            'tube' => 'moveFilesQueue',
        ],
         'updateGigaDBqueue' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,           
            'host' => 'beanstalkd',
            'port' => 11300,
            'tube' => 'updateGigaDB',
        ], 
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
