<?php

return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'em_manuscripts_q' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'host.docker.internal',
            'port' => 11300,
            'tube' => 'em_manuscripts_q',
        ],
        'em_authors_q' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'host.docker.internal',
            'port' => 11300,
            'tube' => 'em_authors_q',
        ],
    ]
];
