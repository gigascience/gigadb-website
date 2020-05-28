<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$config =  [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log','queue','monitor'],
    'aliases' => [
        '@gigadb-data' => '/var',
        '@uploads' => '@gigadb-data/repo',
        '@publicftp'   => '@gigadb-data/ftp/public',
    ],
    'modules' => [
        'monitor' => [
                'class' => \zhuravljov\yii\queue\monitor\Module::class,
            ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'jwt' => [
            'class' => 'sizeg\jwt\Jwt',
            'key'   => $params['jwt_key'],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'filedrop-account',
                    'extraPatterns' => [
                        'POST move/<id>' => 'move',
                    ],
                ],                    
                ['class' => 'yii\rest\UrlRule', 'controller' => 'mockup-url'],
                'site/login' => 'site/login',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user',
                    'extraPatterns' => [
                        'POST lookup' => 'lookup',
                    ],
                ],
            ],
        ],

        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'fs' => [
            'class' => 'creocoder\flysystem\LocalFilesystem',
            'path' => '@gigadb-data',
        ],        
        'queue' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'as jobMonitor' => \zhuravljov\yii\queue\monitor\JobMonitor::class,
            'as workerMonitor' => \zhuravljov\yii\queue\monitor\WorkerMonitor::class,            
            'host' => 'beanstalkd',
            'port' => 11300,
            'tube' => 'moveFilesQueue',
        ],
       'updateGigaDBqueue' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'as jobMonitor' => \common\models\LocalJobMonitor::class,
            'as workerMonitor' => \zhuravljov\yii\queue\monitor\WorkerMonitor::class,            
            'host' => 'beanstalkd',
            'port' => 11300,
            'tube' => 'updateGigaDB',
        ],                

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
