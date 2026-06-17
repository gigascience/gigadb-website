<?php

declare(strict_types=1);

use Codeception\Test\Unit;

/**
 * Unit tests for DatasetAttributes class
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetAttributesTest extends Unit
{
    public function testValidationEmptyValue()
    {
        $da = new DatasetAttributes();
        $this->assertFalse($da->validate());
    }

    public function testValidationBelowMaxLength()
    {
        $da = new DatasetAttributes();
        $da->value = "Lorem ipsum dolor sit amet, consectetuer adipiscing"; //51 chars
        $this->assertTrue($da->validate());
    }

    public function testValidationOverMaxLength()
    {
        $da = new DatasetAttributes();
        $da->value = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec qua"; //201 chars
        $this->assertFalse($da->validate());
    }


    public function testValidationRejectHTML()
    {
        $da = new DatasetAttributes();
        $da->value = "my dodgy tag<script>alert('xss!');</script>";
        $this->assertFalse($da->validate());
    }
}
