<?php

namespace tests\unit\components;

use Yii;

/**
 * Tests ReadmeGenerator component
 */
class ReadmeGeneratorTest extends \Codeception\Test\Unit
{
    /**
     * Test readme string is generated
     */
    public function testGetReadme()
    {
        $doi = "100005";
        $readme = Yii::$app->ReadmeGenerator->getReadme($doi);
        codecept_debug("Readme contents: ".$readme);
        $this->assertTrue(str_contains($readme, "[DOI] 10.5524/100005"), "Readme does not contain starting DOI");
    }
}
