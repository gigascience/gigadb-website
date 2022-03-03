<?php

/**
 * Class FlysystemCest
 *
 * Check that Flysystem and its two adapter are properly configured
 */
class FlysystemCest
{
    public function _after(FunctionalTester $I)
    {
        unlink("/tmp/my.png");
    }

    // tests
    public function tryFlysystemEndToEnd(FunctionalTester $I)
    {
        shell_exec("curl -sS -o /tmp/my.png http://gigadb.test/site/status");
        $I->assertEquals("image/png", mime_content_type("/tmp/my.png") );
    }
}
