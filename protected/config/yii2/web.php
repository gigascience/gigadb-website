<?php

return [
	'id' => 'GigaDB Web Site',
	'basePath' => '/var/www',
    'components' => [
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'encryption' => 'ssl',
                'host' => 'smtp.gmail.com',
                'port' => '465',
                'username' => '',
                'password' => '',
            ],
        ],
    ],
];
?>
