<?php

//load database config
$testdb = json_decode(file_get_contents(dirname(__FILE__).'/db_test.json'), true);

// enable multibyte unicode aware string functions. Only needed for PHP < 5.6. Requires php-mbstring module.

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    require(dirname(__FILE__).'/local.php'),
    array(
        'components'=>array(
            'fixture'=>array(
                'class'=>'system.test.CDbFixtureManager',
            ),
            // uncomment the following to provide test database connection
            'db' => [
                'class' => 'system.db.CDbConnection',
                'connectionString' => "pgsql:dbname={$testdb['database']};host={$testdb['host']}",
                'username' => $testdb['user'],
                'password' => $testdb['password'],
                'charset' => 'utf8',
                'persistent' => true
            ],
        ),
        'params'=>array(
            // For fetching dataset files from @pli888's web server
            'ftp_connection_url' => 'https://gigatools.net',
        ),
    )
);
