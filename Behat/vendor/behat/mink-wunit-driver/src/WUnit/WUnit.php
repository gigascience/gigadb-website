<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

namespace WUnit;

use WUnit\HttpKernel\Client;
use WUnit\Http\YiiKernel;
use WUnit\Http\YiiExitException;
use WUnit\Http\YiiApplication;

class WUnit extends \CComponent
{
    private static $config = array();

    public function init()
    {
        // @todo what with 'header already sent' error?
        error_reporting(E_ERROR);
    }

    public function createClient()
    {
        $client = new Client(new YiiKernel());
        return $client;
    }

    public static function createWebApplication($config = null)
    {
        if ($config !== null) {
            self::$config = $config;
        }

        return new YiiApplication(self::$config);
    }
}
