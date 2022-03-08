<?php

/**
 * Class FlysystemCest
 *
 * Check that Flysystem and its two adapter are properly configured
 */
class FlysystemCest
{
    const TEST_IMAGE_FILE = "/tmp/my.png";

    public function _after(FunctionalTester $I)
    {

        if( file_exists(self::TEST_IMAGE_FILE) )
            unlink(self::TEST_IMAGE_FILE);
    }

    // tests
    public function tryCredentials(FunctionalTester $I)
    {
        $config = require(__DIR__."/../../protected/config/yii2/web.php");
        $I->assertNotEmpty($config['components']['cloudStore']['key']);
        $I->assertNotEmpty($config['components']['cloudStore']['secret']);
    }

    public function tryFlysystemEndToEnd(FunctionalTester $I)
    {
        shell_exec("curl -sS -o ".self::TEST_IMAGE_FILE." http://gigadb.test/site/flysystem-status");
        $I->assertEquals("image/png", mime_content_type(self::TEST_IMAGE_FILE) );
    }
}
