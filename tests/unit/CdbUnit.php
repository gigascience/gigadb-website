<?php

declare(strict_types=1);

use Codeception\Test\Unit;

class CdbUnit extends Unit
{
    protected \CDbConnection $cdbConnection;

    protected function _before() {
        $dbModule = $this->getModule('Db');
        $dsn      = $dbModule->_getConfig('dsn');
        $user     = $dbModule->_getConfig('user');
        $password = $dbModule->_getConfig('password');
        $this->cdbConnection = new \CDbConnection($dsn, $user, $password);
        $this->cdbConnection->active = true;
    }

    protected function _after() {
        $this->cdbConnection->active = false;
    }
}
