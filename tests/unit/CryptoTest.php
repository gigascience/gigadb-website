<?php

/**
 * Tests CryptoService component
 */
class CryptoTest extends \Codeception\Test\Unit
{
    /**
     * Test that random alphanumeric string contains 20 characters
     * Test that random string minus any underscore and dash characters is
     * alphanumeric
     */
    public function testGetRandomString()
    {
        $randomStr = CryptoService::getRandomString();
        $valid_chars = array('-', '_');
        codecept_debug("Random string is: " . $randomStr);
        $this->assertTrue(strlen($randomStr) == 20, "Random string does not contain 20 characters");
        // ctype_alnum() is used to test that $randomStr contains only
        // alphanumeric characters after underscores and dashes are removed
        $this->assertTrue(ctype_alnum(str_replace($valid_chars, "", $randomStr)), "Random string is not alphanumeric");
    }

    /**
     * Verifies hashing algorithm produces expected results
     */
    public function testGetHashedToken()
    {
        $hashValue = "PMzM5S0Q2z6Eg+rc7D/3SkB3c4RQ1PNJzRtQ/bN0/nU=";
        $data = "hobbit";
        $key = "1234567890";
        $result = CryptoService::getHashedToken($key, $data);
        $this->assertEquals($hashValue, $result, "Unexpected hashing result");
    }
}
