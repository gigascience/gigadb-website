<?php

$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
	'id' => 'GigaDB Web Site',
	'basePath' => '/var/www',
	'components' => [
        'jwt' => [
            'class' => 'sizeg\jwt\Jwt',
            'key'   => $params['jwt_key'],
        ],
    ],
];
?>