<?php

return [
	'id' => 'GigaDB Web Site',
	'basePath' => '/var/www',
	'components' => [
        'jwt' => [
            'class' => 'sizeg\jwt\Jwt',
            'key'   => 'dummy--qwertyuiop' #$params['jwt_key'],
        ],
        'fs' => [
            'class' => 'creocoder\flysystem\LocalFilesystem',
            'path' => '@webroot/files',
        ],
     ],
    'params' => $params,
];
?>