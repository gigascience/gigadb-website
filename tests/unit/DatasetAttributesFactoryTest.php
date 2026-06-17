<?php

declare(strict_types=1);

use Codeception\Test\Unit;

class DatasetAttributesFactoryTest extends Unit
{
    public function testItShouldCreateANewInstance()
    {
        $factory = new DatasetAttributesFactory();
        $da = $factory->create();
        $this->assertTrue($da instanceof DatasetAttributes);

        $factory->setAttributeId(100);
        $this->assertEquals(100, $da->attribute_id);

        $factory->setDatasetId(200);
        $this->assertEquals(200, $da->dataset_id);

        $factory->setValue("somevalue");
        $this->assertEquals("somevalue", $da->value);
    }
}
