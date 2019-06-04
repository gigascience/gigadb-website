<?php

namespace GigaDB\Tests\UnitTests;


class ImagesTest extends \CDbTestCase
{
    function testSetIsNoImage() {
        $isNoImage = 1;

        $image = new \Images();
        $image->setIsNoImage($isNoImage);

        $this->assertEquals($isNoImage, $image->is_no_image);
    }

    function testLoadByDataIsNoImage() {
        $data = array(
            'is_no_image' => 1,

        );

        $image = new \Images();
        $image->loadByData($data);

        $this->assertEquals('http://gigadb.org/images/data/cropped/no_image.png', $image->url);
        $this->assertEquals('no_image.jpg', $image->location);
        $this->assertEquals('no image icon', $image->tag);
        $this->assertEquals('Public domain', $image->license);
        $this->assertEquals('GigaDB', $image->photographer);
        $this->assertEquals(1, $image->is_no_image);

        $this->assertTrue($image->validate());
    }

    function testValidateWithIsNoImage() {
        $data = array(
            'is_no_image' => 1,
        );

        $image = new \Images();
        $image->loadByData($data);

        $this->assertTrue($image->validate());
    }

    function testLoadByDataNotIsNoImage() {
        $data = array(
            'is_no_image' => 0,
            'tag' => 'test tag',
            'license' => 'test license',
            'photographer' => 'test photographer',
        );

        $image = new \Images();
        $image->loadByData($data);

        $this->assertEquals('test tag', $image->tag);
        $this->assertEquals('test license', $image->license);
        $this->assertEquals('test photographer', $image->photographer);
        $this->assertEquals(0, $image->is_no_image);
    }

    function testValidateUploadImageRequired() {
        $data = array(
            'is_no_image' => 0,
            'tag' => 'test tag',
            'license' => 'test license',
            'photographer' => 'test photographer',
        );

        $image = new \Images();
        $image->loadByData($data);

        $this->assertFalse($image->validate());
        $errors = $image->getErrors();
        $this->assertEquals('Upload Image cannot be blank.', $errors['image_upload'][0]);
    }

    function testValidateCreditRequired() {
        $data = array(
            'is_no_image' => 0,
            'tag' => 'test tag',
            'license' => 'CC1',
            'photographer' => '',
        );

        $image = new \Images();
        $image->loadByData($data);

        $this->assertFalse($image->validate());
        $errors = $image->getErrors();
        $this->assertEquals('Image Credit cannot be blank.', $errors['photographer'][0]);
    }

    function testSaveWithIsNoImage() {
        $data = array(
            'is_no_image' => 1,
        );

        $image = new \Images();
        $image->loadByData($data);
        $res = $image->save();

        $this->assertTrue($res);

        $res = $image->delete();
        $this->assertTrue($res);
    }
}
