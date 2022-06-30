<?php

$params = [
    'db' => [
        "host" => "reviewdb",
        "port" => "5432",
        "database" => "reviewdb_test",
        "username" => "reviewdb",
        "password" => "testpass",
    ],
    'sftp' => [
        "host" => "sftp_test",
        "username" => "testuser",
        "password" => "testpass",
        "baseDirectory" => "editorialmanager",
    ],
];

return [
    'components' => [
        'em_manuscripts_q' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'host.docker.internal',
            'port' => 11300,
            'tube' => 'em_manuscripts_q.test',
        ],
        'em_authors_q' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'host.docker.internal',
            'port' => 11300,
            'tube' => 'em_authors_q.test',
        ],
    ],
    'params' => $params,
];
