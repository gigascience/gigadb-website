<?php

/**
 * Class AttributeTest
 *
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:test unit AttributeTest
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run unit AttributeTest
 *
 */
class AttributeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

//     tests
    public function testValidateStructuredCommentNameForExistingOne()
    {
        // Create a new attribute model
        $attribute = new Attribute();

        // Set the structured_comment_name attribute to a value that exists in the database
        $existingAttribute = "lat_lon";
        $attribute->structured_comment_name = $existingAttribute;

        // Perform the checking
        $attribute->validateStructuredCommentName('structured_comment_name', []);

        $this->assertFalse($attribute->hasErrors('structured_comment_name'));
    }

    public function testValidateStructuredCommentNameForNonExistOne()
    {
        // Create a new attribute model
        $attribute = new Attribute();

        // Set the structured_comment_name attribute to a value that does not exist in the database
        $nonExistAttribute = "animal_blood_types";
        $attribute->structured_comment_name = $nonExistAttribute;

        // Perform the checking
        $attribute->validateStructuredCommentName('structured_comment_name', []);

        $this->assertTrue($attribute->hasErrors('structured_comment_name'));
        $this->assertEquals($nonExistAttribute . " does not exist.", $attribute->getError('structured_comment_name'));
    }
}
