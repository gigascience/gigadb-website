<?php

// load database config
$dbConfig = json_decode(file_get_contents(dirname(__FILE__).'/db_test.json'), true);

// enable multibyte unicode aware string functions. Only needed for PHP < 5.6. Requires php-mbstring module.
ini_set('mbstring.internal_encoding','UTF-8');

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			 // uncomment the following to provide test database connection
			'db'=>array(
				'connectionString'=>"pgsql:dbname={$dbConfig['database']};host={$dbConfig['host']}",
			),

		),
	)
);
