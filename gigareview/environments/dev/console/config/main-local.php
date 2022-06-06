<?php

return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'manuscripts_q' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'host.docker.internal',
            'port' => 11300,
            'tube' => 'em_manuscripts_q',
        ],
        'authors_q' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'host.docker.internal',
            'port' => 11300,
            'tube' => 'em_authors_q',
        ],
        'reviewers_q' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'host.docker.internal',
            'port' => 11300,
            'tube' => 'em_reviewers_q',
        ],
        'reviewersQuestionsResponses_q' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'host.docker.internal',
            'port' => 11300,
            'tube' => 'em_rev_questions_q',
        ],
        'reviews_q' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'host' => 'host.docker.internal',
            'port' => 11300,
            'tube' => 'em_reviews_q',
        ],
    ]
];
