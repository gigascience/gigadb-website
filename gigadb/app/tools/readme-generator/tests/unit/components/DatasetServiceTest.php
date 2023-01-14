<?php

namespace tests\unit\components;

use app\components\DatasetService;

/**
 * Tests DatasetService component in file-worker application
 */
class DatasetServiceTest extends \Codeception\Test\Unit
{
    /**
     * Test readme string is generated
     */
    public function testGetReadme()
    {
        $doi = "100005";
        $readme = DatasetService::getReadme($doi);
        codecept_debug("Readme contents: ".$readme);
        $this->assertTrue(str_contains($readme, "[DOI] 10.5524/100005"), "Readme does not contain starting DOI");
    }
}
